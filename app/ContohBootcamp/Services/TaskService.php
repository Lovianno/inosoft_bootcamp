<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;

class TaskService {
	private TaskRepository $taskRepository;

	public function __construct() {
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: untuk mengambil semua tasks di collection task
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan task
	 */
	public function addTask(array $data)
	{
		$taskId = $this->taskRepository->create($data);
		return $taskId;
	}

	/**
	 * NOTE: UNTUK mengambil data task
	 */
	public function getById(string $taskId)
	{
		$task = $this->taskRepository->getById($taskId);
		return $task;
	}

	/**
	 * NOTE: untuk update task
	 */
	public function updateTask(array $editTask, array $formData)
	{
		if(isset($formData['title']))
		{
			$editTask['title'] = $formData['title'];
		}

		if(isset($formData['description']))
		{
			$editTask['description'] = $formData['description'];
		}

		$id = $this->taskRepository->save( $editTask);
		return $id;
	}

	public function deleteTask($task){
		$this->taskRepository->deleteTask($task);
	}

	public function save($task)
	{
		$taskId = $this->taskRepository->save($task);
		return $taskId;
	}
	// public function createSubtask($task)
	// {
	// 	$taskId = $this->taskRepository->save($task);
	// 	return $taskId;
	// }
	public function deleteSubTask($task, $subtaskId){

		$subtasks = isset($task['subtasks']) ? $task['subtasks'] : [];
		
		$subtasks = array_filter($subtasks, function($subtask) use($subtaskId) {

			if($subtask['_id'] == $subtaskId)
			{	
				// hapus dari array
				return false;
			} else {
				return true;
			}
		});

		$subtasks = array_values($subtasks);
		$task['subtasks'] = $subtasks;

		$id = $this->taskRepository->save($task);
		return $id;
	}
}