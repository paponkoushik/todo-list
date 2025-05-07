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
    createErrors: {},
    updateErrors: {}
  }),

  getters: {
    taskCreateErrors: (state) => state.createErrors,
    taskUpdateErrors: (state) => state.updateErrors
  },

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
      this.createErrors = {}
      try {
        await api.post('/tasks', task);
      } catch (error) {
        console.log('calling', error.response?.status === 422);
        if (error.response?.status === 422) {
          this.createErrors = error.response.data.errors;
          console.log('papon', this.createErrors);
          
        } else {
          this.createErrors = { general: ["An unexpected error occurred"] };
        }
        return false;
      }
    },

    async updateTask(id, task) {
      this.updateErrors = {}
      try {
        await api.put(`/tasks/${id}`, task);
        return true;
      } catch (error) {
        if (error.response?.status === 422) {
          this.updateErrors = error.response.data.errors;
        } else {
          this.updateErrors = { general: ["An unexpected error occurred"] };
        }
        return false;
      }
    },

    resetUpdateErrors() {
      this.updateErrors = {};
    },
    

    async markComplete(id, task) {
      try {
        await api.put(`/mark-complete/${id}`, task);
      } catch (error) {
        console.error(error);
        throw new Error('Failed to mark complete');
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
