<template>
  <div class="calendar">
    <!--nav from rooms-->
    <div class="row">
      <div class="col-sm-12 col-md-12">
        <ul class="nav nav-tabs">
          <li role="presentation" v-for="room in rooms" :key="room.id">
            <a href="#" @click="getInfoRoom(room)">{{room.name}}</a>
          </li>
        </ul>
      </div>
    </div>
    <!--end nav from rooms-->
    <div class="row">
      <div class="col-sm-12 col-md-12 ">
        <div class="selected_room">{{select_room_name}}</div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12">
        <!--calendar-->
        <nav aria-label="Page navigation">
          <ul class="pagination">
            <li>
              <a href="#" aria-label="Previous" @click="previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li><a href="#">{{month}} {{currentYear}}</a></li>
            <li>
              <a href="#" aria-label="Next" @click="next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-10 col-md-10">
        <table class="table table-bordered">
          <thead>
            <tr>
              <td v-for="(wday, index) in weekDay" :key="index" class="week-day">{{wday}}</td>
            </tr>
          </thead>
          <tbody>
              <tr v-for="(i,index) in dates" :key="index">
                <td v-for="(day,index) in i" :key="index" >
                  <day-section :day='day' :hformat='hour_format' :room='select_room_name' @updateCalendar="updateCalendar"></day-section>
                  
                </td>
              </tr v-if="i.end">
          </tbody>
        </table>
      </div>
    <!--end calendar-->
    <!--rightbar-->
    <div class="col-sm-2 col-md-2">
      <div class="right-bar">
        <button class="btn btn-auth" @click="changeStartWeek">Change start week</button>
        <button class="btn book"><router-link  :to="'/reservation/'+select_room_id">Book it!</router-link></button>
      </div>
    </div>
    <!-- end rightbar-->
  </div>

  </div>
</template>

<script>
import axios from 'axios'
import DaySection from './DaySection.vue'
export default {
  name: 'calendar',
  props: ['hour_format'],
  data () {
    return {
      currentYear: 0,
      currentMonth: 0,
      monthes : ["January", "February",
                 "March", "April", 
                 "May", "June", 
                 "July", "August", 
                 "September","October",
                 "November","December"],
      weekDayM : ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
      weekDayS : ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
      weekDay: [],
      month: "",
      indexes: [],
      dayOfWeek: 0,
      rooms: [],
      select_room_id: 0,
      select_room_name: "",
      events: [],
      dates: [],
      startWeek:false
    }    
  },
  components: {
		'day-section': DaySection
  },
  created(){
      var now = new Date()
      this.currentYear = now.getFullYear() 
      this.currentMonth = now.getMonth()
      var d = now.getDate()
      
      this.getRooms()
      this.getInfoRoom({'id':this.select_room_id, 'name':this.select_room_name}) 

      this.weekDay = this.weekDayM
  },
  methods:{
    updateCalendar(){
       this.getInfoRoom({'id':this.select_room_id, 'name':this.select_room_name}) 
    },

    getRooms(){
      var self = this
      axios.get(serverUrl+'BOOKER/client/api/room/', this.config)
        .then(function (response) {
          self.rooms = response.data.data
          self.select_room_id = self.rooms[0].id
          self.select_room_name = self.rooms[0].name
          self.getInfoRoom(self.rooms[0])     
      })
      .catch(function (error) {
        console.log(error);
      });
    },

    getInfoRoom(room){
      this.select_room_id = room.id
      this.select_room_name = room.name
      this.getDataForMonth()
    },

    previous(){
      this.currentMonth--;
      if(this.currentMonth == 0){
        this.currentMonth = 11
        this.currentYear--
      }
      this.getDataForMonth()
    },

    next(){
      this.currentMonth++;
      if(this.currentMonth == 12){
        this.currentMonth = 0
        this.currentYear++
      }
      this.getDataForMonth()
    },

    getDataForMonth(){
      var self = this
      var cur_month = this.getUnixTimestamp(this.currentYear, this.currentMonth, 1,0,0)
      var daysInCurrentMonth = 32- new Date(this.currentYear,this.currentMonth , 32).getDate(); 

      axios.get(serverUrl+'BOOKER/client/api/event/'+ this.select_room_id + '/' + cur_month + '/' + daysInCurrentMonth, this.config)
        .then(function (response) {
          self.events = response.data.data
          self.getDates()
        })
        .catch(function (error) {
          //console.log(error);
        });
    },

    getUnixTimestamp : function(year, month, date, hours, minutes) {
      return Math.floor(new Date(year, month, date, hours, minutes, 0, 0).getTime() / 1000);
    },

    timestampToDate: function(timestamp) {
      return  new Date(timestamp * 1000) 
    },

    getDates(){
      var temp_dates = []
      var data = new Date(this.currentYear,this.currentMonth,1);  // Определяем текущую дату. 
      data.setDate(1);   // Устанавливаем в переменной первое число текущего месяца. 
      this.dayOfWeek = data.getDay();   // Определяем день недели. 
      if (this.dayOfWeek == 0)   
        this.dayOfWeek = 7;

      this.currentMonth = data.getMonth();   // Узнаём месяц. 
      this.currentYear = data.getFullYear(); //Определяем год

      var daysInCurrentMonth = 32- new Date(this.currentYear,this.currentMonth , 32).getDate(); 
      var prevMonth = this.currentMonth-1; if (this.currentMonth==0) prevMonth=11;
      var prevYear = this.currentYear; if (this.currentMonth==0) prevYear = this.currentYear-1;
      var daysInPrevMonth = 32- new Date(prevYear,prevMonth , 32).getDate(); 

      var today = data.getDate();// Узнаём текущее число. 
    
      this.month = this.monthes[this.currentMonth]

      var days_num = []
      var id = 0
      var pushfl = 0, count = 0
      for(var i = 2 - this.dayOfWeek; i <= 43 - this.dayOfWeek; i++){
        if ((i - 1 + this.dayOfWeek) % 7 == 1)
          days_num = []
        id = 0
        if(i <= 0)
          id =  -(daysInPrevMonth + i)
        else if (i > daysInCurrentMonth)
          id =  -(i - daysInCurrentMonth) 
        else 
          id = i 

        if(this.startWeek) --id

        if(id>0)  days_num.push({'day':id, 'cur':true, 'day_event':this.events[id]})
        else {
          days_num.push({'day':'', 'cur':false, 'day_event':[]})
          pushfl++
        }
        count++
      

        if ((i - 1 + this.dayOfWeek) % 7 == 0 ){
          if (pushfl != 7) 
            temp_dates.push(days_num)  
          count = 0
          pushfl = 0  
        }
      }

      this.dates =  temp_dates
    },

  changeStartWeek(){  
    this.startWeek = !this.startWeek
    if(this.startWeek)
        this.weekDay = this.weekDayS
      else 
        this.weekDay = this.weekDayM
    this.getDataForMonth()    
  }
}

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.calendar{
  margin-top: -70px;
}

.week-day{
  color: red;
   font-size:11pt; 
   background-color:lightblue;
}

.right-bar{
  text-align: center;
}

.book{
  margin-top: 50px;
}
</style>
