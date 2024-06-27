<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Create a Controller in Modular structure
 *
 * @package App\Commands
 * @author Solomon Ochepa <solomonochepa@gmail.com>
 */
class ModuleController extends BaseCommand
{
    /** @var String $group Group */
    protected $group       = 'Module';

    /** @var String $name Command's name */
    protected $name        = 'module:controller';

    /** @var String $description Command description */
    protected $description = 'Generates a new Controller file.';

    /** @var String $usage Command usage */
    protected $usage        = 'module:controller [name] [module] [options]';

    /**
     * @var String $example Command example */
    protected $example      = 'module:controller ExampleController Example';

    /** @var Array $arguments the Command's Arguments */
    protected $arguments    = [
        'name'      => 'The Controller class name.',
        'module'    => 'The module name.'
    ];

    /** @var Array $options the Command's Options */
    protected $options = [
        '-v' => 'Views',
        '--model' => 'Model',
    ];

    /** @var String $controller Controller name */
    protected $controller;

    /** Module Name */
    protected $module;
    protected $module_lower;
    protected $module_plural;
    protected $module_lower_plural;

    protected $model;

    /** @var String $module_path Modules absolute path. */
    protected $module_path;

    /** @var String $module_basename Modules relative path | basename. */
    protected $module_basename;

    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');

        // Controller name
        while (!isset($params[0])) {
            CLI::error("NOTICE:\t\tThe Controller name field is required.");

            CLI::write("USAGE:\t\t{$this->usage}", "green");
            CLI::write("EXAMPLE:\t{$this->example}\n", "green");

            $input = CLI::prompt('Controller');
            if (CLI::strlen($input)) {
                $params[0] = $input;
            }
        }

        if (strlen(preg_replace('/[^A-Za-z0-9]+/', '', $params[0])) <> mb_strlen($params[0])) {
            CLI::error("Controller class name must be plain ascii characters A-z, and can contain numbers 0-9");
            return;
        }

        // Module name
        while (!isset($params[1])) {
            CLI::error("NOTICE:\t\tThe Module name field is required.");

            CLI::write("USAGE:\t\t{$this->usage}", "green");
            CLI::write("EXAMPLE:\t{$this->example}\n", "green");

            $input = CLI::prompt('Module', "{$params[0]}");
            if (CLI::strlen($input)) {
                $params[1] = $input;
            }
        }

        if (strlen(preg_replace('/[^A-Za-z0-9]+/', '', $params[1])) <> mb_strlen($params[1])) {
            CLI::error("Module name must be plain ascii characters A-z, and can contain numbers 0-9");
            return;
        }

        $this->controller           = $this->str_title($params[0]);
        $this->module               = ucfirst($params[1]);
        $this->module_plural        = plural($this->module);
        $this->module_lower         = strtolower($this->module);
        $this->module_lower_plural  = plural($this->module_lower);
        $this->module_basename      = basename(APPPATH) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->module;
        $this->module_path          = APPPATH . '..' . DIRECTORY_SEPARATOR . $this->module_basename;

        // Confirm that module exists.
        if (!is_dir($this->module_path)) {
            CLI::error("Module [{$this->module}] not found.\n");
            return;
        }

        // CLI::getOption('f') == ''

        $this->module_path = realpath($this->module_path);

        try {
            $this->createController();
        } catch (\Exception $e) {
            CLI::error($e);
        }
    }

    /**
     * Create controller file
     */
    protected function createController()
    {
        $controllers_path   = $this->createDir('Controllers');
        $this->model        = "{$this->module}Model";
        $class              = $this->controller;
        $file               = $controllers_path . DIRECTORY_SEPARATOR . $class . '.php';

        if (!file_exists($file)) {
            $template = "<?php

namespace {$this->module}\\Controllers;

use App\\Controllers\\BaseController;
use {$this->module}\\Models\\{$this->model};

class {$class} extends BaseController
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        \$this->data = [
            'table' => '{$this->module_lower}',
            'title' => '{$this->module}',
            'route' => '{$this->module_lower}',
            'view'  => '{$this->module}',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \$this->templates->generateLayout(\$this->data['view'] . '\index', \$this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \$this->data['title'] = 'Tambah Data {$this->module}';
        \$this->data['newRecord'] = true;
        
        return \$this->templates->generateLayout(\$this->data['view'] . '\input', \$this->data);
    }

    /**
     * Save a newly created resource in storage.
     */
    public function onCreate()
    {
        if(\$this->request->isAJAX() && \$this->request->getMethod() == 'post'){
            \$model = model({$this->model}::class);
            \$request = \$this->request->getPost();

            if(!\$this->validate(\$model->rules())) {
                return \$model->getErrorRules();
            }else {
                if(\$model->save(\$request)){
                    session()->setFlashdata('success_msg', 'Data berhasil ditambahkan');
                    \$response['success'] = base_url(\$this->data['route']);
                    return json_encode(\$response);
                }
            }
        }else {
            // throw new \CodeIgniter\Exceptions\PageNotFoundException('Bad Request');
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to(\$this->data['route']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \$id
     */
    public function update(\$id)
    {
        \$model = model({$this->model}::class)
            ->find(base64_decode(\$id));
        
        if(!empty(\$model)){
            \$this->data['title'] = 'Ubah Data ' . \$model[''];
            \$this->data['model'] = \$model;
            \$this->data['newRecord'] = false;

            return \$this->templates->generateLayout(\$this->data['view'] . '\input', \$this->data);
        }else {
            session()->setFlashData('error_msg', 'Data tidak ditemukan');
            return redirect()->to(\$this->data['route']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function onUpdate()
    {
        if(\$this->request->isAJAX() && \$this->request->getMethod() == 'post'){
            \$model = model({$this->model}::class);
            \$request = \$this->request->getPost();

            if(!\$this->validate(\$model->rules())) {
                return \$model->getErrorRules();
            }else {
                if(\$model->save(\$request)){
                    session()->setFlashdata('success_msg', 'Data berhasil diubah');
                    \$response['success'] = base_url(\$this->data['route']);
                    return json_encode(\$response);
                }
            }            
        }else {
            // throw new \CodeIgniter\Exceptions\PageNotFoundException('Bad Request');
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to(\$this->data['route']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function onDelete()
    {
        if(\$this->request->getMethod() == 'delete'){
            \$request = \$this->request->getPost();
            \$model = model({$this->model}::class);

            if(\$model->delete(base64_decode(\$request['id']))){
                return redirect()->to(\$this->data['route'])->with('info_msg', 'Data berhasil dihapus');
            }
        }else {
            // throw new \CodeIgniter\Exceptions\PageNotFoundException('Bad Request');
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to(\$this->data['route']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \$id
     */
    public function detail(\$id)
    {
        \$model = model({$this->model}::class)
            ->find(base64_decode(\$id));
            
        if(!empty(\$model)){
            \$model['id'] = \$id;
            \$this->data['title'] = 'Detail Data ' . \$model[''];
            \$this->data['model'] = \$model;

            return \$this->templates->generateLayout(\$this->data['view'] . '\detail', \$this->data);
        }else {
            session()->setFlashData('error_msg', 'Data tidak ditemukan');
            return redirect()->to(\$this->data['route']);
        }
    }
}
";

            file_put_contents($file, $template);
            CLI::write("Controller: {$file}");
        } else {
            CLI::error("Controller allready exists!");
        }
    }

    /**
     * Create module directory and set, if required, gitkeep to keep this in git.
     */
    protected function createDir(string $folder, bool $gitkeep = false): string
    {
        $dir = $this->module_path . DIRECTORY_SEPARATOR .  $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            if ($gitkeep) {
                file_put_contents($dir .  '/.gitkeep', '');
            }
        }

        return $dir;
    }

    public function str_chained(string $text)
    {
        // Replace uppercase letters with lowercase letters and prepend them with an underscore
        $outputString = preg_replace_callback('/([A-Z])/', function ($matches) {
            return '_' . strtolower($matches[1]);
        }, $text);

        // Remove any leading underscores
        $outputString = ltrim($outputString, '_');

        return $outputString;
    }

    public function str_title(string $text)
    {
        $words = explode('_', $text);
        $titleCaseWords = array_map('ucfirst', $words);
        $titleCase = implode('', $titleCaseWords);

        // Remove any leading underscores
        $titleCase = ltrim($titleCase, '_');

        return $titleCase;
    }
}
