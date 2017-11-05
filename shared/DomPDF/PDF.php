<?php

namespace Shared\DomPDF;

use Nova\Config\Repository as ConfigRepository;
use Nova\Filesystem\Filesystem;
use Nova\Http\Response;
use Nova\View\Factory as ViewFactory;

use Dompdf\Dompdf;
use Dompdf\Options;

use Exception;


class PDF
{
    /**
     * @var Dompdf
     */
    protected $dompdf;

    /**
     * @var \Nova\Config\Repository
     */
    protected $config;

    /**
     * @var \Nova\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Nova\View\Factory
     */
    protected $views;

    protected $rendered = false;

    protected $orientation;
    protected $paper;
    protected $showWarnings;
    protected $public_path;


    /**
     * @param \Dompdf $dompdf
     * @param \Nova\Config\Repository $config
     * @param \Nova\Filesystem\Filesystem $files
     * @param \Nova\View\Factory $view
     */
    public function __construct(Dompdf $dompdf, ConfigRepository $config, Filesystem $files, ViewFactory $views)
    {
        $this->dompdf = $dompdf;
        $this->config = $config;
        $this->files  = $files;
        $this->views  = $views;

        // Show the Dompdf warnings?
        $this->showWarnings = $this->config->get('dompdf.show_warnings', false);
    }

    /**
     * Get the DomPDF instance
     *
     * @return Dompdf
     */
    public function getDomPDF()
    {
        return $this->dompdf;
    }

    /**
     * Set the paper size (default A4)
     *
     * @param string $paper
     * @param string $orientation
     * @return $this
     */
    public function setPaper($paper, $orientation = 'portrait')
    {
        $this->paper = $paper;

        $this->orientation = $orientation;

        $this->dompdf->setPaper($paper, $orientation);

        return $this;
    }

    /**
     * Show or hide warnings
     *
     * @param bool $warnings
     * @return $this
     */
    public function setWarnings($warnings)
    {
        $this->showWarnings = $warnings;

        return $this;
    }

    /**
     * Load a HTML string
     *
     * @param string $string
     * @param string $encoding Not used yet
     * @return static
     */
    public function loadHTML($string, $encoding = null)
    {
        $string = $this->convertEntities($string);

        $this->dompdf->loadHtml($string, $encoding);

        $this->rendered = false;

        return $this;
    }

    /**
     * Load a HTML file
     *
     * @param string $file
     * @return static
     */
    public function loadFile($file)
    {
        $this->dompdf->loadHtmlFile($file);

        $this->rendered = false;

        return $this;
    }

    /**
     * Load a View and convert to HTML
     *
     * @param string $view
     * @param array $data
     * @param array $module
     * @param string $encoding Not used yet
     * @return static
     */
    public function loadView($view, $data = array(), $module = null, $encoding = null)
    {
        $html = $this->views->make($view, $data, $module)->render();

        $html = preg_replace('/>\s+</', '><', $html);

        return $this->loadHTML($html, $encoding);
    }

    /**
     * Set/Change an option in DomPdf
     *
     * @param array $options
     * @return static
     */
    public function setOptions(array $options)
    {
        $options = new Options($options);

        $this->dompdf->setOptions($options);

        return $this;
    }

    /**
     * Output the PDF as a string.
     *
     * @return string The rendered PDF as string
     */
    public function output()
    {
        if(! $this->rendered) {
            $this->render();
        }

        return $this->dompdf->output();
    }

    /**
     * Save the PDF to a file
     *
     * @param $fileName
     * @return static
     */
    public function save($fileName)
    {
        $this->files->put($fileName, $this->output());

        return $this;
    }

    /**
     * Make the PDF downloadable by the user
     *
     * @param string $fileName
     * @return \Nova\Http\Response
     */
    public function download($fileName = 'document.pdf')
    {
        $output = $this->output();

        return new Response($output, 200, array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' =>  'attachment; filename="' .$fileName .'"'
            ));
    }

    /**
     * Return a response with the PDF to show in the browser
     *
     * @param string $fileName
     * @return \Nova\Http\Response
     */
    public function stream($fileName = 'document.pdf')
    {
        $output = $this->output();

        return new Response($output, 200, array(
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="' .$fileName .'"',
        ));
    }

    /**
     * Render the PDF
     */
    protected function render()
    {
        if (! $this->dompdf) {
            throw new Exception('DOMPDF not created yet');
        }

        $this->dompdf->setPaper($this->paper, $this->orientation);

        $this->dompdf->render();

        if ($this->showWarnings) {
            global $_dompdf_warnings;

            if(! empty($_dompdf_warnings) && count($_dompdf_warnings)) {
                $warnings = '';

                foreach ($_dompdf_warnings as $msg){
                    $warnings .= $msg . "\n";
                }

                if(! empty($warnings)) {
                    //throw new Exception($warnings);
                }
            }
        }

        $this->rendered = true;
    }


    protected function convertEntities($subject)
    {
        $entities = array(
            '€' => '&#0128;',
            '£' => '&pound;',
        );

        foreach($entities as $search => $replace) {
            $subject = str_replace($search, $replace, $subject);
        }

        return $subject;
    }
}
