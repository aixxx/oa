<?php
namespace App\Services\Message;

use Exception;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\PhpEngine;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class StringCompilerEngine extends PhpEngine
{
    /**
     * @var BladeCompiler
     */
    protected $compiler;

    /**
     * StringCompilerEngine constructor.
     *
     * @param BladeCompiler $compiler
     */
    public function __construct(BladeCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    public function renderString($__tpl, $__data = [])
    {
        $__compiled = $this->compiler->compileString($__tpl);
        $obLevel    = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);
        try {
            eval('?> ' . $__compiled);
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        } catch (Throwable $e) {
            $this->handleViewException(new FatalThrowableError($e), $obLevel);
        }

        return ltrim(ob_get_clean());
    }
}
