<?php
include('config.php');
$div="<div style='border: 1px solid silver; padding: 10px; background-color: whitesmoke; border-radius: 5px;'>";
$divH="<div style='border: 1px solid silver; padding: 10px; background-color: Gainsboro; border-radius: 5px;'>";
if (isset($_GET['term'])) {
    $searchTerm = trim($_GET['term']);

    if ($searchTerm === "") {
        echo "Please enter a search term.";
        return;
    }

    $directories = [$dir.'sessions', $dir.'tests']; // Added 'pdfs' directory
    $imagesFolder = 'images';
    $results = '';

    foreach ($directories as $directory) {
        foreach (glob($directory . '/*') as $filename) {
            // Skip index.php files
            if (basename($filename) === 'index.php') {
                continue;
            }

            $content = file_get_contents($filename);
            $displayFilename = str_replace(["sessions/", "tests/", "pdfs/", "_session.txt"], "", $filename);
            $isTestFolder = (strpos($filename, $dir.'tests/') !== false);

            // Search in file content
            if (stripos($content, $searchTerm) !== false) {
                $linkPrefix = $isTestFolder ? 'test-result.php?file=' : 'login.php?session=';
                $highlightedDisplayFilename = preg_replace('/' . preg_quote($searchTerm, '/') . '/i', '<mark>$0</mark>', $displayFilename);
                $results .= "$divH<h3>" . ($isTestFolder ? 'Test result content: ' : '') . "<a href='$linkPrefix" . urlencode($displayFilename) . "'>$highlightedDisplayFilename</a></h3>";

                $lines = explode("\n", $content);
                foreach ($lines as $line) {
                    if (stripos($line, $searchTerm) !== false) {
                        if (preg_match('/\[file: (.+)\]/', $line, $match)) {
                            $imageFilename = $match[1];
                            $line = explode('[', $line);
                            $highlightedLine = preg_replace('/' . preg_quote($searchTerm, '/') . '/i', '<mark>$0</mark>', $line[0]);
                            $imagePath = $imagesFolder . '/' . basename($imageFilename);
                            if (file_exists($imagePath)) {
                                $results .= "<p>$div $highlightedLine <br><a href='$imagePath' target='blank'><img src='$imagePath' alt='$imageFilename' class='thumbnail' style='object-fit: cover; margin-right: 10px; border-radius: 2px; box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);'></a></div>";
                            } else {
                                $results .= "<p>$div$highlightedLine</div></p>";
                            }
                        } elseif (preg_match('/\[pdf:\s*(.+?)\]/i', $line, $match)) {
                            $pdfFilename = $match[1];
                            $pdfPath = $dir.'pdfs/' . basename($pdfFilename);
                            $line = explode('[', $line);
                            $highlightedLine = preg_replace('/' . preg_quote($searchTerm, '/') . '/i', '<mark>$0</mark>', $line[0]);
                            if (file_exists($pdfPath)) {
                                $results .= "<p>$div $highlightedLine <br><a href='$pdfPath' target='_blank'><img src='lib/img/pdf.png' width='50' alt='$imageFilename' style='object-fit: cover; margin-right: 10px; border-radius: 2px; box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);'></a></div></p>";
                            } else {
                                $results .= "<p>$div$highlightedLine</div></p>";
                            }
                        } else {
                            $highlightedLine = preg_replace('/' . preg_quote($searchTerm, '/') . '/i', '<mark>$0</mark>', $line);
                            $results .= "<p>$div$highlightedLine</div></p>";
                        }
                    }
                }
                $results .= "</div><hr>";
            }

            // Search in filename
            if (stripos($displayFilename, $searchTerm) !== false) {
                $linkPrefix = $isTestFolder ? 'test-result.php?file=' : 'login.php?session=';
                $highlightedDisplayFilename = preg_replace('/' . preg_quote($searchTerm, '/') . '/i', '<mark>$0</mark>', $displayFilename);
                $results .= "$divH<h3>" . ($isTestFolder ? 'Test results: ' : '') . "<a href='$linkPrefix" . urlencode($displayFilename) . "'>$highlightedDisplayFilename</a></h3>";
                $results .= "<p><b>\"" . preg_replace('/' . preg_quote($searchTerm, '/') . '/i', '<mark>$0</mark>', $searchTerm) . "\"</b> found in record name</p>";
                $results .= "</div><hr>";
            }
        }
    }

    if ($results === '') {
        echo "No matches found.";
    } else {
        echo $results;
    }
}
?>
