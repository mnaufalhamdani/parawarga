<?php

namespace Modules\Master\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterAreaTable extends Migration
{
    public function up()
    {
        $this->forge->addField('id');
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'area_name'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'kelurahan_id'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'address'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'        => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'created_by'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
            'updated_by'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('master_area', true);
    }

    public function down()
    {
        $this->forge->dropTable('master_area', true);
    }
}
