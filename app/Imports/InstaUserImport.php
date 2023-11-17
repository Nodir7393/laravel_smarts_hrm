<?php

namespace App\Imports;

use Modules\Instagram\Entities\InstaUser;
use Maatwebsite\Excel\Concerns\ToModel;

class InstaUserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new InstaUser([
            "username" => $row[0]
        ]);
    }
}
