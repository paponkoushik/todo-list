<script setup>
import { ref, onMounted } from 'vue'
import { useTaskStore } from '@/stores/task'

const taskStore = useTaskStore()
const tasks = ref([])
const newTask = ref({ title: '', body: '' })
const editingTask = ref(null)

const fetchTasks = async (page = 1) => {
  tasks.value = await taskStore.getTasks(page)
}

const createTask = async () => {
  await taskStore.createTask(newTask.value)
  newTask.value = { title: '', body: '' }
  fetchTasks()
}

const deleteTask = async (id) => {
  if (confirm('Are you sure you want to delete this task?')) {
    await taskStore.deleteTask(id)
    fetchTasks()
  }
}

const openEditModal = (task) => {
  editingTask.value = { ...task }
}

const updateTask = async () => {
  await taskStore.updateTask(editingTask.value.id, editingTask.value)
  editingTask.value = null
  fetchTasks()
}

const toggleComplete = async (task) => {
  await taskStore.updateTask(task.id, {
    ...task,
    is_completed: !task.is_completed
  })
  fetchTasks()
}

onMounted(() => {
  fetchTasks()
})
</script>

<template>
  <div class="app-container">
    <div class="todo-app">
      <header class="app-header">
        <h1 class="app-title">My Todo List</h1>
        <p class="app-subtitle">Organize your day with ease</p>
      </header>

      <!-- Create Task Form -->
      <form @submit.prevent="createTask" class="task-form">
        <div class="form-group">
          <input 
            v-model="newTask.title" 
            placeholder="Task title" 
            class="form-input"
            required
          />
        </div>
        <div class="form-group">
          <textarea
            v-model="newTask.body"
            placeholder="Task description"
            class="form-textarea"
            rows="2"
          ></textarea>
        </div>
        <button type="submit" class="add-button">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Task
        </button>
      </form>

      <!-- Tasks List -->
      <div class="task-list-container">
        <ul class="task-list">
          <li v-for="task in tasks" :key="task.id" class="task-item" :class="{ completed: task.is_completed }">
            <div class="task-content">
              <div class="task-checkbox">
                <input 
                  type="checkbox" 
                  :checked="task.is_completed" 
                  @change="toggleComplete(task)" 
                  class="checkbox-input"
                />
                <span class="checkmark"></span>
              </div>
              <div class="task-details">
                <h3 class="task-title">{{ task.title }}</h3>
                <p class="task-description">{{ task.body }}</p>
              </div>
            </div>
            <div class="task-actions">
              <button 
                v-if="!task.is_completed" 
                @click="toggleComplete(task)" 
                class="action-button complete-button"
                title="Mark as complete"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="openEditModal(task)" class="action-button edit-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
              </button>
              <button @click="deleteTask(task.id)" class="action-button delete-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </li>
        </ul>

        <!-- Empty State -->
        <div v-if="tasks.length === 0" class="empty-state">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <h3>No tasks yet</h3>
          <p>Add your first task to get started</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination" v-if="taskStore.pagination.last_page > 1">
        <button
          v-for="page in taskStore.pagination.last_page"
          :key="page"
          @click="fetchTasks(page)"
          :class="['page-button', page === taskStore.pagination.current_page ? 'active' : '']"
        >
          {{ page }}
        </button>
      </div>

      <!-- Edit Modal -->
      <div v-if="editingTask" class="modal-overlay">
        <div class="modal">
          <div class="modal-header">
            <h3>Edit Task</h3>
            <button @click="editingTask = null" class="modal-close-button">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Title</label>
              <input v-model="editingTask.title" class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea v-model="editingTask.body" class="form-textarea" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="editingTask = null" class="cancel-button">Cancel</button>
            <button @click="updateTask" class="save-button">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.app-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 2rem;
  background-color: #f9fafb;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.todo-app {
  width: 100%;
  max-width: 800px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  padding: 2rem;
}

/* Header Styles */
.app-header {
  margin-bottom: 2rem;
  text-align: center;
}

.app-title {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
}

.app-subtitle {
  font-size: 1rem;
  color: #6b7280;
}

/* TODO: Form Styles */
.task-form {
  margin-bottom: 2rem;
  background-color: #f3f4f6;
  padding: 1.5rem;
  border-radius: 8px;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #111827;
}

.form-input, .form-textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

button {
  text-decoration: none;
}

.add-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background-color: #6366f1;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.add-button:hover {
  background-color: #4f46e5;
}

.add-button svg {
  width: 1.25rem;
  height: 1.25rem;
}

/* TODO: Task List Styles */
.task-list-container {
  margin-bottom: 2rem;
}

.task-list {
  list-style: none;
}

.task-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.2s;
}

.task-item:last-child {
  border-bottom: none;
}

.task-item:hover {
  background-color: #f3f4f6;
}

.task-item.completed {
  opacity: 0.8;
}

.task-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.task-checkbox {
  position: relative;
  display: inline-block;
  width: 20px;
  height: 20px;
  margin-top: 2px;
}

.checkbox-input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: white;
  border: 2px solid #e5e7eb;
  border-radius: 4px;
  transition: all 0.2s;
}

.checkbox-input:checked ~ .checkmark {
  background-color: #6366f1;
  border-color: #6366f1;
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
  left: 6px;
  top: 2px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-input:checked ~ .checkmark:after {
  display: block;
}

.task-details {
  flex: 1;
}

.task-title {
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.25rem;
  word-break: break-word;
}

.task-item.completed .task-title {
  text-decoration: line-through;
  color: #6b7280;
}

.task-description {
  color: #6b7280;
  font-size: 0.875rem;
  word-break: break-word;
}

.task-item.completed .task-description {
  text-decoration: line-through;
}

.task-actions {
  display: flex;
  gap: 0.5rem;
}

.action-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  border: none;
  background-color: transparent;
  cursor: pointer;
  transition: background-color 0.2s;
}

.action-button:hover {
  background-color: #f3f4f6;
}

.action-button svg {
  width: 1.25rem;
  height: 1.25rem;
}

.complete-button svg {
  color: #10b981;
}

.complete-button:hover {
  background-color: #f3f4f6;
}
.edit-button svg {
  color: #6366f1;
}

.delete-button svg {
  color: #ef4444;
}

/* TODO:Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  color: #6b7280;
}

.empty-state svg {
  margin-bottom: 1rem;
  color: #e5e7eb;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #111827;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.page-button {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  background-color: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-button:hover {
  background-color: #f3f4f6;
}

.page-button.active {
  background-color: #6366f1;
  color: white;
  border-color: #6366f1;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal {
  background-color: white;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.modal-close-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.modal-close-button:hover {
  background-color: #f3f4f6;
}

.modal-close-button svg {
  width: 1.5rem;
  height: 1.5rem;
  color: #6b7280;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.cancel-button {
  padding: 0.5rem 1rem;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  color: #111827;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-button:hover {
  background-color: #f3f4f6;
}

.save-button {
  padding: 0.5rem 1rem;
  background-color: #6366f1;
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.save-button:hover {
  background-color: #4f46e5;
}

/*TODO: Responsive Adjustments */
@media (max-width: 640px) {
  .app-container {
    padding: 1rem;
  }
  
  .todo-app {
    padding: 1.25rem;
  }
  
  .task-content {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .task-actions {
    align-self: flex-end;
  }
}
</style>