
    <div class="card " style=" height:200px;">
       <h3 class=" font-normal text-xl py-4 -ml-4 mb-3 border-l-4 border-blue-500 pl-4 ">
       <a href="{{$project->path()}}" class=" text-black no-underline">{{$project->title}}</a>  
       </h3>
       <div class=" text-gray-600">{{ Illuminate\Support\Str::limit($project->description, 50) }}</div>
       </div>
 