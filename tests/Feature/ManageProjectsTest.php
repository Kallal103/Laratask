<?php

namespace Tests\Feature;

use App\Project;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;


class ManageProjectsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

     use WithFaker, RefreshDatabase;

     public function test_guests_can_not_manage_projects(){
        //$this->withoutExceptionHandling();
        $project = factory('App\Project')->create();
        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get('/projects/edit')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
        
       
    }     
    

    public function test_a_user_can_create_a_project(){
        //$this->withoutExceptionHandling();
        $this->signIn();
        $this->get('projects/create')->assertStatus(200);
        $attributes = [
         'title' => $this->faker->sentence(4),
         'description' => $this->faker->sentence(4),
         'notes'       => 'General Notes here .'
        ];
        $response = $this->post('/projects', $attributes);
        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        //$this->assertDatabaseHas('projects', $attributes);

        $this->get($project->path())
        ->assertSee($attributes['title'])
        ->assertSee($attributes['description']);
        //->assertSee($attributes['notes']);
    }


    public function test_a_user_can_update_a_project(){
        // $this->signIn();
        // $this->withoutExceptionHandling();
        
        // $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
        ->patch($project->path(),
            $attributes= ['title'=>'Changed',
            'description'=>'Changed',
            'notes' => 'Changed'])->assertRedirect($project->path());

            $this->get($project->path().'/edit')->assertOk();

        $this->assertDatabaseHas('projects', $attributes);
    }

    function test_a_user_can_update_a_projects_general_notes(){
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
        ->patch($project->path(),
            $attributes= ['notes' => 'Changed']);

          

        $this->assertDatabaseHas('projects', $attributes);  

    }

    public function test_a_user_can_view_their_project(){
        
       
        // $this->signIn();
        // //$this->withoutExceptionHandling();
        
        // $project = factory('App\Project')->create(['owner_id' => auth()->id()]);
        $project = ProjectFactory::create();
        $this->actingAs($project->owner)
        ->get($project->path())
        ->assertSee($project->title)
        ->assertSee($project->description);
    }

    public function test_an_authenticated_user_cannot_view_the_projects_of_others(){
        
       //$this->withoutExceptionHandling();
        $this->signIn();
       
        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }
 public function test_an_authenticated_user_cannot_update_the_projects_of_others(){
        
       //$this->withoutExceptionHandling();
        $this->signIn();
       
        $project = factory('App\Project')->create();

        $this->patch($project->path(),[])->assertStatus(403);
    }

    public function test_a_project_requires_a_title(){
        $this->signIn();
        $attributes = factory('App\Project')->raw(['title'=>'']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    public function test_a_project_requires_a_description(){
        $this->signIn();
        $attributes = factory('App\Project')->raw(['description'=>'']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

   

    
}
