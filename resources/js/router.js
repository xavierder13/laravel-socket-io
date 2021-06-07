import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Login from './auth/Login.vue';
import Dashboard from './views/dashboard/Dashboard.vue';
import UserIndex from './views/user/UserIndex.vue';
import Permission from './views/permission/PermissionIndex.vue';
import Role from './views/role/RoleIndex.vue';
import ActivityLogs from './views/activity_logs/ActivityLogs.vue';
import PageNotFound from './404/PageNotFound.vue';
import Unauthorize from './401/Unauthorize.vue';

Vue.use(Router);

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    children: [
      {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        beforeEnter(to, from, next)
        { 
          let user_permissions = JSON.parse(localStorage.getItem("user_permissions"));
          // if(user_permissions.includes('project-list') || user_permissions.includes('project-create'))
          // {
          //   next();
          // }
          // else
          // {
          //   next('/unauthorize');
          // }
          next();
        }
      },
      {
        path: '/user/index',
        name: 'user.index',
        component: UserIndex,
        beforeEnter(to, from, next)
        { 
          let user_permissions = JSON.parse(localStorage.getItem("user_permissions"));
          if(user_permissions.includes('user-list') || user_permissions.includes('user-create'))
          {
            next();
          }
          else
          {
            next('/unauthorize');
          }
        }
      },
      {
        path: '/permission/index',
        name: 'permission.index',
        component: Permission,
        beforeEnter(to, from, next)
        { 
          let user_permissions = JSON.parse(localStorage.getItem("user_permissions"));
          if(user_permissions.includes('permission-list') || user_permissions.includes('permission-create'))
          {
            next();
          }
          else
          {
            next('/unauthorize');
          }
        }
      },
      {
        path: '/role/index',
        name: 'role.index',
        component: Role,
        beforeEnter(to, from, next)
        { 
          let user_permissions = JSON.parse(localStorage.getItem("user_permissions"));
          if(user_permissions.includes('role-list') || user_permissions.includes('role-create'))
          {
            next();
          }
          else
          {
            next('/unauthorize');
          }
        }
      },
      {
        path: '/activity_logs',
        name: 'activity_logs',
        component: ActivityLogs,
        beforeEnter(to, from, next)
        { 
          let user_permissions = JSON.parse(localStorage.getItem("user_permissions"));
          if(user_permissions.includes('activity-logs'))
          {
            next();
          }
          else
          {
            next('/unauthorize');
          }
        }
      },
      {
        path: '/unauthorize',
        name: 'unauthorize',
        component: Unauthorize,
      }
    ],
    beforeEnter(to, from, next) {

      if (localStorage.getItem('access_token')) {
        next();
      }
      else {
        next('/login');
      }
    }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter(to, from, next) {
      
      if (localStorage.getItem('access_token')) {
        next('/');
      }
      else {
        next();
      }
    }
  },
  {
    path: '*',
    component: PageNotFound,
  },
];

const router = new Router({
  routes: routes
});

export default router;