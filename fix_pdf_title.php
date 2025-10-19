<?php

// Improved convertHtmlToPdf method
protected function convertHtmlToPdf(string $html, ?Application $application = null): ?string
{
    $disk = config('apple.documents_disk', 'local');
    
    // Use a more descriptive temporary filename
    $tmpHtml = tempnam(sys_get_temp_dir(), 'afs_app_') . '.html';
    $tmpPdf = tempnam(sys_get_temp_dir(), 'afs_app_') . '.pdf';
    
    $pdfTitle = 'AFS Application';
    if ($application) {
        $status = $application->status;
        $seasonYear = $application->season ? (app(\App\Services\PrintService::class))->getSeasonYearSlug($application->season) : '';
        $pdfTitle = "AFS_Application-{$status}" . ($seasonYear ? "-{$seasonYear}" : "");
    }
    
    // Ensure HTML has proper structure and title
    $html = $this->ensureHtmlTitle($html, $pdfTitle);
    
    file_put_contents($tmpHtml, $html);

    $chromiumPath = '/usr/bin/chromium-browser';
    $process = new \Symfony\Component\Process\Process([
        $chromiumPath,
        '--headless',
        '--disable-gpu',
        '--no-sandbox',
        '--print-to-pdf=' . $tmpPdf,
        '--no-pdf-header-footer',
        '--virtual-time-budget=1000', // Add this to ensure proper rendering
        'file://' . $tmpHtml
    ]);
    $process->run();

    @unlink($tmpHtml);

    if ($process->isSuccessful() && file_exists($tmpPdf)) {
        // Save intermediate PDF if in debug mode and application is provided
        if (config('app.debug') && $application) {
            $status = $application->status;
            $pdfFileName = "AFS_Application-{$status}-answers.pdf";
            $pdfPath = $application->getApplicationPdfPath($pdfFileName);
            Storage::disk($disk)->put($pdfPath, file_get_contents($tmpPdf));
        }
        return $tmpPdf;
    } else {
        Log::error("Chromium failed to convert HTML to PDF", [
            'command' => $process->getCommandLine(),
            'output' => $process->getOutput(),
            'error_output' => $process->getErrorOutput(),
        ]);
    }
    return null;
}

/**
 * Ensure HTML has proper structure and title
 */
private function ensureHtmlTitle(string $html, string $title): string
{
    // Check if HTML has a proper DOCTYPE and structure
    if (!preg_match('/<!DOCTYPE/i', $html)) {
        $html = "<!DOCTYPE html>\n" . $html;
    }
    
    // Ensure we have an html tag
    if (!preg_match('/<html/i', $html)) {
        $html = "<html>\n" . $html . "\n</html>";
    }
    
    // Handle title replacement more robustly
    if (preg_match('/<title[^>]*>.*?<\/title>/is', $html)) {
        // Replace existing title
        $html = preg_replace('/<title[^>]*>.*?<\/title>/is', "<title>{$title}</title>", $html);
    } else {
        // Check if we have a head tag
        if (preg_match('/<head[^>]*>/i', $html)) {
            // Insert title after head tag
            $html = preg_replace('/(<head[^>]*>)/i', "$1\n<title>{$title}</title>", $html, 1);
        } else {
            // Create head section with title
            if (preg_match('/<html[^>]*>/i', $html)) {
                $html = preg_replace('/(<html[^>]*>)/i', "$1\n<head>\n<title>{$title}</title>\n</head>", $html, 1);
            } else {
                // Wrap everything in proper HTML structure
                $html = "<!DOCTYPE html>\n<html>\n<head>\n<title>{$title}</title>\n</head>\n<body>\n{$html}\n</body>\n</html>";
            }
        }
    }
    
    return $html;
}