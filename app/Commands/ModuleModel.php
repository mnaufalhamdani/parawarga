<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Create a Model in Modular structure
 *
 * @package App\Commands
 * @author Solomon Ochepa <solomonochepa@gmail.com>
 */
class ModuleModel extends BaseCommand
{
    /**
     * Group
     *
     * @var string
     */
    protected $group       = 'Module';

    /**
     * Command's name
     *
     * @var string
     */
    protected $name        = 'module:model';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Generates a new module Model file.';

    /**
     * Command usage
     *
     * @var string
     */
    protected $usage        = 'module:model [name] [module] [options]';

    /**
     * Command example
     *
     * @var string
     */
    protected $example      = 'module:model ExampleModel Example';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments    = [
        'name'      => 'The model class name.',
        'module'    => 'The module name.'
    ];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [
        '-s' => 'Seed',
        '-m' => 'Migration',
        '-c' => 'Controller',
        '-v' => 'Views',
        '-t|--table' => 'Table',
    ];

    protected $model;

    /**
     * Module Name
     */
    protected $module;
    protected $module_lower;
    protected $module_plural;
    protected $module_lower_plural;

    /**
     * Module folder (default /Modules)
     */
    protected $module_path;

    /** @var String $module_basename Modules root dir. */
    protected $module_basename;

    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');

        // Model name
        while (!isset($params[0])) {
            CLI::error("NOTICE:\t\tThe Model name field is required.");

            CLI::write("USAGE:\t\t{$this->usage}", "green");
            CLI::write("EXAMPLE:\t{$this->example}\n", "green");

            $input = CLI::prompt('Model');
            if (CLI::strlen($input)) {
                $params[0] = $input;
            }
        }

        if (strlen(preg_replace('/[^A-Za-z0-9]+/', '', $params[0])) <> mb_strlen($params[0])) {
            CLI::error("Model class name must be plain ascii characters A-z, and can contain numbers 0-9");
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

        $this->model                = ucfirst($params[0]);
        $this->module               = ucfirst($params[1]);
        $this->module_lower         = strtolower($this->module);
        $this->module_plural        = plural($this->module);
        $this->module_lower_plural  = strtolower($this->module_plural);
        $this->module_basename      = basename(APPPATH) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->module;
        $this->module_path          = APPPATH . '..' . DIRECTORY_SEPARATOR . $this->module_basename;

        // Confirm module.
        if (!is_dir($this->module_path)) {
            CLI::error("Module [{$this->module}] not found.\n");
            return;
        }

        // CLI::getOption('f') == ''

        // dd($params);

        $this->module_path = realpath($this->module_path);

        try {
            $this->createModel();
        } catch (\Exception $e) {
            CLI::error($e);
        }
    }

    /**
     * Create model file
     */
    protected function createModel()
    {
        $models_path    = $this->createDir('Models');
        $entity         = $this->model;
        $class          = $this->model . "Model";
        $db_table       = strtolower($this->model);
        $file           = $models_path . DIRECTORY_SEPARATOR . $class . '.php';

        if (!file_exists($file)) {
            $template = "<?php

namespace {$this->module}\\Models;

use CodeIgniter\Model;

class {$class} extends Model
{
    protected \$table               = '{$db_table}';
    protected \$primaryKey          = 'id';
    protected \$useAutoIncrement    = true;
    protected \$returnType          = 'array';
    protected \$useSoftDeletes      = false;
    protected \$protectFields       = true;
    protected \$allowedFields       = [];
    protected bool \$allowEmptyInserts   = true;

    // Dates
    protected \$useTimestamps       = true;
    protected \$dateFormat          = 'datetime';
    protected \$createdField        = 'created_at';
    protected \$updatedField        = 'updated_at';
    protected \$deletedField        = 'deleted_at';

    // Validation
    protected \$validationRules     = [
        // 'active'        => 'trim|permit_empty|in_list[0,1]',
        // 'title'         => 'trim|required|string|min_length[3]|max_length[32]|is_unique[permissions.title]',
        // 'slug'          => 'trim|permit_empty|max_length[32]|is_unique[permissions.slug]',
        // 'description'   => 'trim|permit_empty|max_length[255]',
    ];
    protected \$validationMessages  = [];
    protected \$skipValidation       = false;
    protected \$cleanValidationRules = true;

    // Callbacks
    protected \$allowCallbacks = true;
    protected \$beforeInsert   = [];
    protected \$afterInsert    = [];
    protected \$beforeUpdate   = [];
    protected \$afterUpdate    = [];
    protected \$beforeFind     = [];
    protected \$afterFind      = [];
    protected \$beforeDelete   = [];
    protected \$afterDelete    = [];

    public function rules() {
        return \$this->validationRules;
    }

    public function getErrorRules() {
        \$validation = \Config\Services::validation();
        foreach(\$this->validationRules as \$key => \$val) {
            \$errorValidation[] = [
                'name'      => \$key,
                'message'   => \$validation->getError(\$key)
            ];
        }

        \$data['error'] = \$errorValidation;

        return json_encode(\$data);
    }
}
";

            file_put_contents($file, $template);
            CLI::write("Model: [{$file}]");
        } else {
            CLI::error("Model allready exists!");
        }
    }

    /**
     * create module Dir
     *
     * Create directory and set, if required, gitkeep to keep this in git.
     *
     * @param type $folder
     * @param type $gitkeep
     * @return string
     */
    protected function createDir($folder, $gitkeep = false)
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
}
