<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Schedule_Table extends CI_Migration
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
              'date' => array(
                 'type' => 'DATE',
                 'null' => false,
              ),
              'place_id' => array(
                 'type' => 'INT',
                 'null' => false,
              ),
              'interval' => array(
                 'type' => 'DECIMAL',
                 'constraint' => '10,2',
                 'null' => false,
                 'default' => 0.00
              ),
              'type' => array(
                 'type' => 'BIT',
                 'null' => false,
              ),
              'category_id' => array(
                 'type' => 'INT',
                 'null' => false,
              ),
              'limit' => array(
                 'type' => 'INT',
                 'null' => true,
              ),
              'waktu' => array(
                 'type' => 'TIME',
                 'null' => false,
              ),
              'duration' => array(
                 'type' => 'INT',
                 'null' => true,
              ),
           )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('schedules');
    }

    public function down()
    {
        $this->dbforge->drop_table('schedules');
    }
}

?>