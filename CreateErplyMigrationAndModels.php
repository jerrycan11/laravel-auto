<?php

namespace App\Console\Commands;

use App\Services\ErplyAPI;
use Illuminate\Console\Command;

class CreateErplyMigrationAndModels extends Command
{
    protected $signature = 'create:mm';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->createErplyTables();
        #$this->createModels();
    }

    protected function createModels()
    {
        $erplyList = $this->getErplyNameList();
        foreach ($erplyList as $value) {
            $str = 'Erply' . substr($value, 3);
            $str2 = 'erply_' . strtolower(substr($value, 3));
            $filename = $str . ".php";
            $myfile = fopen($filename, "w") or die("Unable to open file!");
            $this->writeLine($myfile, '<?php');
            $this->writeLine($myfile, 'namespace App\Models;');
            $this->writeLine($myfile, 'use Illuminate\Database\Eloquent\Model;');
            $this->writeLine($myfile, 'class ' . $str . ' extends Model');
            $this->writeLine($myfile, '{');
            $this->writeLine($myfile, 'public $table = "' . $str2 . '";');
            $this->writeLine($myfile, '}');
            fclose($myfile);
        }
    }

    protected function getErplyNameList()
    {
        $erplyList = [
            'getCustomers',
            'getEmployees',
            'getSuppliers',
            'getAddresses',
            'getCurrencies',
            'getCustomerGroups',
            'getGiftCards',
            'getPointsOfSale',
            'getPriceLists',
            'getProductGroups',
            'getProductCategories',
            'getProducts',
            'getServices',
            'getSupplierGroups',
            'getWarehouses',
            'getVatRates',
            'getInventoryRegistrations',
            'getInventoryTransfers',
            'getInventoryWriteOffs',
            'getPayments',
            'getCampaigns',
            'getCoupons',
            'getEvents',
            'getPurchaseDocuments',
            'getSalesDocuments'
        ];
        return $erplyList;
    }

    protected function createErplyTables()
    {
        $erplyList = $this->getErplyNameList();
        $erply = ErplyAPI::getErply();
        $erply->bulk = false;
        foreach ($erplyList as $value) {
            sleep(1);
            $responses = $erply->send([
                'request' => $value
            ]);
            $str = 'erply_' . strtolower(substr($responses->status->request, 3));
            $str2 = 'Erply' . substr($responses->status->request, 3);
            $name = date("Y_m_d_gis") . '_' . $str;
            print_r($responses->status->request . "\n");
            if (sizeof($responses->records) > 0) {
                $filename = $name . ".php";
                $myfile = fopen($filename, "w") or die("Unable to open file!");
                $this->writeLine($myfile, '<?php');
                $this->writeLine($myfile, 'use Illuminate\Database\Migrations\Migration;');
                $this->writeLine($myfile, 'use Illuminate\Database\Schema\Blueprint;');
                $this->writeLine($myfile, 'use Illuminate\Support\Facades\Schema;');
                $this->writeLine($myfile, ' ');
                $this->writeLine($myfile, 'class ' . $str2 . ' extends Migration');
                $this->writeLine($myfile, '{');
                $this->writeLine($myfile, 'public function up()');
                $this->writeLine($myfile, '{');
                $this->writeLine($myfile, 'Schema::create("' . $str . '", function (Blueprint $table) {');
                $this->writeLine($myfile, '$table->id();');
                foreach ($responses->records[0] as $key => $item) {
                    if ($key == 'id') {
                        $this->writeLine($myfile, '$table->string("eid")->nullable();');
                    } else {
                        if (is_array($item)) {
                            $this->writeLine($myfile, '$table->text("' . $key . '")->nullable();');
                        } elseif (is_int($item)) {
                            $this->writeLine($myfile, '$table->integer("' . $key . '")->nullable();');
                        } elseif (is_double($item)) {
                            $this->writeLine($myfile, '$table->double("' . $key . '")->nullable();');
                        } else {
                            $this->writeLine($myfile, '$table->string("' . $key . '")->nullable();');
                        }
                    }

                }
                $this->writeLine($myfile, '$table->timestamps();');
                $this->writeLine($myfile, '});');
                $this->writeLine($myfile, '}');
                $this->writeLine($myfile, ' ');
                $this->writeLine($myfile, 'public function down()');
                $this->writeLine($myfile, '{');
                $this->writeLine($myfile, 'Schema::dropIfExists("' . $str . '");');
                $this->writeLine($myfile, '}');
                $this->writeLine($myfile, '}');
                fclose($myfile);

                $str = 'Erply' . substr($value, 3);
                $str2 = 'erply_' . strtolower(substr($value, 3));
                $filename = $str . ".php";
                $myfile = fopen($filename, "w") or die("Unable to open file!");
                $this->writeLine($myfile, '<?php');
                $this->writeLine($myfile, 'namespace App\Models;');
                $this->writeLine($myfile, 'use Illuminate\Database\Eloquent\Model;');
                $this->writeLine($myfile, 'class ' . $str . ' extends Model');
                $this->writeLine($myfile, '{');
                $this->writeLine($myfile, 'public $table = "' . $str2 . '";');
                $this->writeLine($myfile, '}');
                fclose($myfile);


            }
        }
    }

    protected function writeLine($myfile, $line)
    {
        $txt = $line . "\n";
        fwrite($myfile, $txt);
        print_r($txt . "\n");
    }
}


