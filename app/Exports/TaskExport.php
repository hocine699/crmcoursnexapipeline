<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskStage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $data=Task::all()->where('created_by', \Auth::user()->creatorId());



        foreach($data as $k => $task)
        {

            $user=User::where('id',$task->user_id)->first();
            $users=isset($user->name) ?$user->name :'' ;

            $priority=Task::$priority[$task->priority];


            $stage=TaskStage::where('id',$task->stage)->first();
            $stages=$stage->name;

            //$priority=Task::$priority[$task->priority];

            $parent_id=Task::parents($task->parent);

            // $parent=$parent_id['1'];

            $created=User::where('id',$task->created_by)->first();
            $created_by=$created->name;


            $data[$k]["user_id"]=$users;
            $data[$k]["stage"]=$stages;
            $data[$k]["priority"]=$priority;
            // $data[$k]["parent_id"]=$parent;
            $data[$k]["created_by"]=$created_by;

        }

        return $data;
    }

     public function headings(): array
    {
        return [
            "Task ID",
            "User",
            "Name",
            "status",
            "stage",
            "priority",
            "start_date",
            "due_date",
            "parent",
            "Parent_id",
            "Description",
            "Attachment",
            "Created_By",
            "Created_At",
            "Updated_At",
        ];
    }
}
