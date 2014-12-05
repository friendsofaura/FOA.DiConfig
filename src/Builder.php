<?php
namespace FOA\DiConfig;

use crodas\ClassInfo\Definition\TFunction;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use crodas\ClassInfo\ClassInfo;
use ReflectionClass;
use Exception;

class Builder
{
    private $alias = array(
        'Aura\Dispatcher\Dispatcher' => 'aura/web-kernel:dispatcher',        
        'Aura\Router\Router' => 'aura/web-kernel:router',
        'Aura\Web\Request' => 'aura/web-kernel:request',
        'Aura\Web\Response' => 'aura/web-kernel:response',
        'Aura\Web\ResponseSender' => 'aura/web-kernel:response_sender',
    );
    private $parser;
    
    public function __construct()
    {
        $this->parser = new ClassInfo;
    }

    public function fromDirectory($path, $framework = true)
    {
        $class_params = array();
        $dir = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $file_path = $file->getRealPath();
            $class_params = array_merge($class_params, $this->getClassParams($file_path));
        }
        return $this->toString($class_params, $framework);
    }

    public function fromFile($file_path, $framework = true)
    {
        $class_params = $this->getClassParams($file_path);
        return $this->toString($class_params, $framework);
    }

    protected function toString($class_params, $framework = true)
    {
        $text = '';
        foreach ($class_params as $class => $params) {
            foreach ($params as $param => $type_hint) {                
                $text .= '$di->params[\'' . $class . '\'][\'' . $param . '\'] = ';
                if ($this->alias[$type_hint] && $framework) {
                    $text .= '$di->lazyGet(\'' . $this->alias[$type_hint] . '\');' . PHP_EOL;
                } else {
                    $text .= '$di->lazyNew(\'' . $type_hint . '\');' . PHP_EOL;
                }                
            }
        }
        return $text;
    }

    protected function getClassParams($file_path)
    {
        $class_params = array();
        $this->parser->parse($file_path);
        foreach ($this->parser->getClasses() as $class) {
            try {
                $reflection = new ReflectionClass((string) $class);
                $method = $reflection->getConstructor();
                if ($method) {
                    $parameters = $method->getParameters();
                    foreach($parameters as $param) {
                        $class_name = '';
                        $object = $param->getClass();
                        if ($object) {
                            $class_name = $object->name;
                        }
                        $class_params[(string)$class][$param->name] = $class_name;                    
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
        return $class_params;
    }
}
