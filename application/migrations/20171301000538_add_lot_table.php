<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Lot_Table extends CI_Migration
{
    public function up()
    {

        $this->dbforge->add_field(
           array(
              'id' => array(
                 'type' => 'INT',
                 'constraint' => 5,
                 'unsigned' => true,
                 'auto_increment' => true
              ),
              'no_lot' => array(
                 'type' => 'INT',
                 'null' => false,
              ),
              'no_va' => array(
                 'type' => 'VARCHAR',
                 'constraint' => 50,
                 'null' => false,
              ),
              'reason' => array(
                 'type' => 'TEXT',
                 'null' => true,
              ),
              'status' => array(
                 'type' => 'TINYINT',
                 'null' => false,
              ),
              'schedule_id' => array(
                 'type' => 'INT',
                 'null' => false,
              ),
              'stock_id' => array(
                 'type' => 'INT',
                 'null' => false,
              ),
              'stock_name' => array(
                 'type' => 'VARCHAR',
                 'constraint' => 100,
                 'null' => false,
              )
           )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('lots');
    }

    public function down()
    {
        $this->dbforge->drop_table('lots');
    }
}

?>