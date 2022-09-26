<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller {
	private TaskService $taskService;
	public function __construct() {
		$this->taskService = new TaskService();
	}

	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks);
	}

	public function createTask(Request $request)
	{
		$request->validate([
			'title'=>'required|string|min:3',
			'description'=>'required|string'
		]);

		$data = [
			'title'=>$request->post('title'),
			'description'=>$request->post('description')
		];

		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->taskService->addTask($dataSaved);
		$task = $this->taskService->getById($id);

		return response()->json($task);
	}


	public function updateTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required|string',
			'title'=>'string',
			'description'=>'string',
			'assigned'=>'string',
			'subtasks'=>'array',
		]);

		$taskId = $request->post('task_id');
		$formData = $request->only('title', 'description', 'assigned', 'subtasks');
		$task = $this->taskService->getById($taskId);

		$this->taskService->updateTask($task, $formData);

		$task = $this->taskService->getById($taskId);

		return response()->json($task);
	}


	// TODO: deleteTask()
	public function deleteTask(Request $request)
	{
		// $mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->task_id;

		// $existTask = $mongoTasks->find(['_id'=>$taskId]);
		$task = $this->taskService->getById($taskId);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}
		$this->taskService->deleteTask($task);
		// $mongoTasks->deleteQuery(['_id'=>$taskId]);


		return response()->json([
			'message'=> 'Success delete task '.$taskId
		]);
	}

	// TODO: assignTask()
	public function assignTask(Request $request)
	{
		// $mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'=>'required',
			'assigned'=>'required'
		]);

		$taskId = $request->task_id;
		// $assigned = $request->post('assigned');
		$assigned = $request->assigned;
		// $existTask = $mongoTasks->find(['_id'=>$taskId]);
		$task = $this->taskService->getById($taskId);
		
		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$task['assigned'] = $assigned;

		$this->taskService->save($task);
		// $mongoTasks->save($existTask);

		// $task = $mongoTasks->find(['_id'=>$taskId]);
		$task = $this->taskService->getById($taskId);

		return response()->json($task);
	}

	// TODO: unassignTask()
	public function unassignTask(Request $request)
	{
		// $mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->task_id;

		// $existTask = $mongoTasks->find(['_id'=>$taskId]);
		$task = $this->taskService->getById($taskId);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$task['assigned'] = null;

		// $mongoTasks->save($existTask);
		$this->taskService->save($task);

		$task = $this->taskService->getById($taskId);

		return response()->json($task);
	}

	// TODO: createSubtask()
	public function createSubtask(Request $request)
	{
		// $mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'=>'required',
			'title'=>'required|string',
			'description'=>'required|string'
		]);

		$taskId = $request->task_id;
		$title = $request->title;
		$description = $request->description;

		// $existTask = $mongoTasks->find(['_id'=>$taskId]);
		$task = $this->taskService->getById($taskId);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$subtasks = isset($task['subtasks']) ? $task['subtasks'] : [];

		$subtasks[] = [
			'_id'=> (string) new \MongoDB\BSON\ObjectId(),
			'title'=>$title,
			'description'=>$description
		];

		$task['subtasks'] = $subtasks;

		$this->taskService->save($task);
		$task = $this->taskService->getById($taskId);
		// $task = $mongoTasks->find(['_id'=>$taskId]);

		return response()->json($task);
	}

	// TODO deleteSubTask()
	public function deleteSubtask(Request $request)
	{
		
		$request->validate([
			'task_id'=>'required',
			'subtask_id'=>'required'
		]);

		$taskId = $request->task_id;
		$subtaskId = $request->subtask_id;

		// $existTask = $mongoTasks->find(['_id'=>$taskId]);
		$task = $this->taskService->getById($taskId);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$this->taskService->deleteSubTask($task, $subtaskId);

		$task = $this->taskService->getById($taskId);

		return response()->json($task);
	}

}