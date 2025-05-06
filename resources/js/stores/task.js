import { defineStore } from 'pinia';
import api from '../plugins/axios';

export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [],
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
    },
  }),

  actions: {
    async getTasks(page = 1) {
      try {
        const response = await api.get(`/tasks?page=${page}`);
        this.tasks = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
        };
        return this.tasks;
      } catch (error) {
        console.error(error);
        throw new Error('Failed to fetch tasks');
      }
    },

    async createTask(task) {
      try {
        await api.post('/tasks', task);
      } catch (error) {
        console.error(error);
        throw new Error('Failed to create task');
      }
    },

    async updateTask(id, task) {
      try {
        await api.put(`/tasks/${id}`, task);
      } catch (error) {
        console.error(error);
        throw new Error('Failed to update task');
      }
    },

    async deleteTask(id) {
      try {
        await api.delete(`/tasks/${id}`);
      } catch (error) {
        console.error(error);
        throw new Error('Failed to delete task');
      }
    },
  },
});
