import Vue from 'vue'
import Router from 'vue-router'
import Calendar from '@/components/Calendar'
import Reservation from '@/components/Reservation'
import Registration from '@/components/Registration'
import EmployeeList from '@/components/EmployeeList'
import EditUser from '@/components/EditUser'
import Event from '@/components/Event'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/calendar',
      name: 'calendar',
      component: Calendar
    },
    {
      path: '/reservation/:room_id',
      name: 'reservation',
      component: Reservation
    },
    {
      path: '/employeeList',
      name: 'employeeList',
      component: EmployeeList,
      children: [
        { path: 'registration', name: 'registration',component: Registration },
        { path: 'editUser/:user_id', name: 'editUser',component: EditUser },
      ]
    },
    {
      path: '/event/:event_id/:hour_format/:room',
      name: 'event',
      component: Event
    },
  ]
})
