<?php 


error_reporting(E_ALL);
ini_set('display_errors', '1');

        if (function_exists('exec')) { echo "exec() is enabled."; } else { echo "exec() is still disabled."; }
        ini_set('disable_functions', ''); // Check if exec() is enabled 
        if (function_exists('exec')) { echo "exec() is enabled."; } else { echo "exec() is still disabled."; }
require_once __DIR__ . '/vendor/Latex/PdfLatex.php';

use PhpLatex\PdfLatex;

// LaTeX code 
$latexCode = ' 
\documentclass{article}
\usepackage{amsmath}
\usepackage{array}

\begin{document}

\title{Account Journal Entries}
\author{}
\date{}
\maketitle

\begin{tabular}{|c|c|c|c|c|}
\hline
\textbf{Date} & \textbf{Account} & \textbf{Description} & \textbf{Debit} & \textbf{Credit} \\\\
\hline
2023-04-06 & Cash & Initial Capital & \$10,000 &  \\\\
\hline
2023-04-07 & Equipment & Purchase of Equipment & \$5,000 &  \\\\
\hline
2023-04-07 & Cash & Payment for Equipment &  & \$5,000 \\\\
\hline
2023-04-08 & Rent Expense & Monthly Rent & \$1,000 &  \\\\
\hline
2023-04-08 & Cash & Payment for Rent &  & \$1,000 \\\\
\hline
\end{tabular}

\end{document}

'; 

$pdflatex = new PhpLatex_PdfLatex();
// Create a new PdfLatex instance 
// $pdflatex = new PdfLatex(); // Compile the LaTeX code to a PDF file 
$pdfPath = $pdflatex->compileString($latexCode); // Set headers to return the PDF file in the HTTP response 
// header('Content-Type: application/pdf'); 
// header('Content-Disposition: inline; filename="document.pdf"'); 
// readfile($pdfPath);

 ?>