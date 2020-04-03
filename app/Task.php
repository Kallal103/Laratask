<?php

namespace App;
use App\Activity;
use App\Project;
use App\Task;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    protected $touches = ['project'];

    protected $casts = [
        'completed' => 'boolean'
    ];
     /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($task) {
            $task->project->recordActivity('created_task');
            // Activity::create([
            //     'project_id' => $task->project->id,
            //     'description' => 'created_task'
            // ]);
        });      
        
        // static::updated(function ($task) {
        //     if(! $task->completed) return;

            

        //     // Activity::create([
        //     //     'project_id' => $task->project->id,
        //     //     'description' => 'completed_task'
        //     // ]);
        // });
    }

    public function complete()
    {
        $this->update(['completed'=> true]);

        $this->project->recordActivity('completed_task');
    }  
      public function incomplete()
    {
        $this->update(['completed'=> false]);

        $this->project->recordActivity('completed_task');
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }
}
