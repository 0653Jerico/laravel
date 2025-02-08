<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Task Manager</h1>

        <!-- Add Task Form -->
        <form id="addTaskForm" class="mb-8 bg-white p-6 rounded-lg shadow">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                    Task Title
                </label>
                <input type="text" id="title" name="title" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea id="description" name="description" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <button type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Add Task
            </button>
        </form>

        <!-- Tasks List -->
        <div id="tasksList" class="space-y-4">
            @foreach($tasks as $task)
            <div class="task-item bg-white p-4 rounded-lg shadow" data-id="{{ $task->id }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" class="task-checkbox mr-3" 
                            {{ $task->is_completed ? 'checked' : '' }}>
                        <div>
                            <h3 class="font-bold task-title {{ $task->is_completed ? 'line-through text-green-500' : '' }}">
                                {{ $task->title }}
                            </h3>
                            <p class="text-gray-600">{{ $task->description }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="show-task bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">View</button>
                        <button class="edit-task bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">Edit</button>
                        <button class="delete-task bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Delete</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- View Task Modal -->
    <div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-xl font-bold mb-4">Task Details</h3>
            <div id="viewTaskContent"></div>
            <div class="mt-4">
                <button onclick="closeViewModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-xl font-bold mb-4">Edit Task</h3>
            <form id="editTaskForm">
                <input type="hidden" id="editTaskId">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                    <input type="text" id="editTitle" class="border rounded w-full py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea id="editDescription" class="border rounded w-full py-2 px-3"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add Task
        document.getElementById('addTaskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const response = await fetch('/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    title: document.getElementById('title').value,
                    description: document.getElementById('description').value,
                    is_completed: false
                })
            });
            if (response.ok) {
                window.location.reload();
            }
        });

        // Toggle Complete
        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', async (e) => {
                const taskItem = e.target.closest('.task-item');
                const taskId = taskItem.dataset.id;
                const taskTitle = taskItem.querySelector('.task-title');
                
                await fetch(`/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        is_completed: e.target.checked
                    })
                });

                // Toggle classes
                if (e.target.checked) {
                    taskTitle.classList.add('line-through', 'text-green-500');
                } else {
                    taskTitle.classList.remove('line-through', 'text-green-500');
                }
            });
        });

        // Delete Task
        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', async (e) => {
                const taskId = e.target.closest('.task-item').dataset.id;
                await fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                window.location.reload();
            });
        });

        // Show Task
        document.querySelectorAll('.show-task').forEach(button => {
            button.addEventListener('click', async (e) => {
                const taskId = e.target.closest('.task-item').dataset.id;
                const response = await fetch(`/tasks/${taskId}`);
                const task = await response.json();
                
                document.getElementById('viewTaskContent').innerHTML = `
                    <p><strong>Title:</strong> ${task.title}</p>
                    <p><strong>Description:</strong> ${task.description || 'No description'}</p>
                    <p><strong>Status:</strong> ${task.is_completed ? 'Completed' : 'Pending'}</p>
                `;
                
                document.getElementById('viewModal').classList.remove('hidden');
            });
        });

        // Edit Task
        document.querySelectorAll('.edit-task').forEach(button => {
            button.addEventListener('click', async (e) => {
                const taskId = e.target.closest('.task-item').dataset.id;
                const response = await fetch(`/tasks/${taskId}`);
                const task = await response.json();
                
                document.getElementById('editTaskId').value = task.id;
                document.getElementById('editTitle').value = task.title;
                document.getElementById('editDescription').value = task.description;
                
                document.getElementById('editModal').classList.remove('hidden');
            });
        });

        // Handle Edit Form Submit
        document.getElementById('editTaskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const taskId = document.getElementById('editTaskId').value;
            
            const response = await fetch(`/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    title: document.getElementById('editTitle').value,
                    description: document.getElementById('editDescription').value
                })
            });
            
            if (response.ok) {
                window.location.reload();
            }
        });

        // Modal Close Functions
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>