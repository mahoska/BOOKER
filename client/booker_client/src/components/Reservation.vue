<template>
  <div class="reservation">
    <div class="row">
      <div class="col-sm-12 col-md-12 ">
        <div class="selected_room">{{room_name}}</div>
      </div>
      </div> 

    <div class="form-group">
      <label>
        1. Booked for
      </label>
      <div class = "row">
        <div  class="col-sm-3 col-md-3">
          <select v-model="user_id" class="form-control">
            <option v-for="user in users" :value="user.id" :key="user.id">
              {{user.fullname}}
            </option>
          </select>
        </div>
        <div  class="col-sm-9 col-md-9"></div>
      </div>
    </div>

    <div class="form-group">
      <label>
        2. I would like to book this meeting:
      </label>
      <div class = "row">
        <div  class="col-sm-2 col-md-2">
          <select v-model="month" class="form-control">
            <option v-for="month_el in monthes" :value="month_el.id" :key="month_el.id">
              {{month_el.name}}
            </option>
          </select>
        </div>
        <div  class="col-sm-1 col-md-1">
          <select v-model="day" class="form-control">
            <option v-for="(day_num, index) in days" :value="day_num" :key="index">
              {{day_num}}
            </option>
          </select>
        </div>
        <div  class="col-sm-2 col-md-2">
          <select v-model="year" class="form-control">
            <option v-for="(year_num,index) in years" :value="year_num" :key="index">
              {{year_num}}
            </option>
          </select>
        </div>
        <div  class="col-sm-7 col-md-7"></div>
      </div>
    </div>

    <div class="form-group">
      <label>
        3. Specify what the time and end  of the meeting 
          (This will be what people see on the calendar.):
      </label>
      <!--if 24 hour time format -->
      <div class="red">{{err_time}}</div>
        <div class = "row" v-if="f24==true">
          <div  class="col-sm-1 col-md-1">
            <select v-model="hour_start" class="form-control">
              <option v-for="(hour_num, index) in hours_24_start" :value="hour_num" :key="index">
                {{hour_num}}
              </option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1">
            <select v-model="minute_start" class="form-control">
              <option  value="0">00</option>
              <option  value="30">30</option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1" style="text-align:center"> - </div>
            <div  class="col-sm-1 col-md-1">
              <select v-model="hour_end" class="form-control">
                <option v-for="(hour_num, index) in hours_24_end" :value="hour_num" :key="index">
                  {{hour_num}}
                </option>
              </select>
            </div>
            <div  class="col-sm-1 col-md-1">
              <select v-model="minute_end" class="form-control">
                <option  value="0">00</option>
                <option  value="30">30</option>
              </select>
            </div>
            <div  class="col-sm-7 col-md-7"></div>
        </div>
        <!--if 12 hour time format -->
        <div class = "row" v-if="f12==true">
          <div  class="col-sm-1 col-md-1">
            <select v-model="hour_start" class="form-control">
              <option v-for="hour_num in hours_12_start" :value="hour_num" :key="hour_num">
                {{hour_num}}
              </option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1">
            <select v-model="minute_start" class="form-control">
              <option  value="0" >00</option>
              <option  value="30">30</option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1">
            <select v-model="day_part_start" class="form-control">
              <option  value="am">AM</option>
              <option  value="pm">PM</option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1" style="text-align:center"> - </div>
          <div  class="col-sm-1 col-md-1">
            <select v-model="hour_end" class="form-control">
              <option v-for="hour_num in hours_12_end" :value="hour_num" :key="hour_num">
                {{hour_num}}
              </option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1">
            <select v-model="minute_end" class="form-control">
              <option  value="0">00</option>
              <option  value="30">30</option>
            </select>
          </div>
          <div  class="col-sm-1 col-md-1">
            <select v-model="day_part_end" class="form-control">
              <option  value="am">AM</option>
              <option  value="pm">PM</option>
            </select>
          </div>
          <div  class="col-sm-5 col-md-5"></div>
        </div>
      </div>

      <div class="form-group">
      <label>
        4.Enter the specifics for the meeting.(This will be what people
          see when they click on an event link.)
      </label>
      <div class="row">
            <div  class="col-sm-5 col-md-5">
              <textarea class="form-control" v-model="description" rows="5" 
                        @keydown="descriptionCheck" 
                        title="permissible symbols: letters of the Latin alphabet, numbers, and also !?()=: . , ; ' № % $ -">
              </textarea>
              <p class="err_small">{{err_desc}}</p>
            </div>
            <div  class="col-sm-7 col-md-7"></div>
          </div>
      </div> 

      <div class="form-group">
        <label>
          5. Is this going to be a recurring event?
        </label>
        <div class="radio">
          <label>
            <input type="radio" name="recur" id="recur1" value="1" v-model="is_recur" >yes
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="recur" id="recur2" value="0" v-model="is_recur" >no
          </label>
        </div>
      </div>   

      <div class="form-group" v-if="is_recurive_event==true">
        <label>
          6. If it is recurring, specify weekly, bi-weekly, or monthly.
        </label>
        <div class="radio">
          <label>
            <input type="radio" name="recur_period" id="recurp1" value="w" v-model="recur_period" >weekly
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="recur_period" id="recurp2" value="b" v-model="recur_period" >bi-weekly
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="recur_period" id="recurp3" value="m" v-model="recur_period" >monthly
          </label>
        </div>
         
        <label> 
          If weekly or bi-weekly, specify the number of weeks for it to keep recurring. 
          If monthly, specify the number of months. 
          (If you choose "bi-weekly" and put in an odd number of weeks,
          the computer will round down.)
        </label>  
        <div class="row">
          <div  class="col-sm-1 col-md-1"> 
            <input class="form-control num_only" v-model="duration"
                  @keydown="durationCheck" 
                  oninput="if(this.value>10) this.value = this.value%10  "/>
          </div>
          <div  class="col-sm-2 col-md-2"> 
            <label v-if="recur_period=='w'">duration(max 4 weeks)</label>
            <label v-if="recur_period=='b'">duration(max 2 weeks)</label>
            <label v-if="recur_period=='m'">duration(max 1 month)</label>
          </div>
          <div  class="col-sm-9 col-md-9"> </div>
        </div>
      </div>   
      <!-- result block-->
      <div class="red">{{err_add}}</div>
      <div>
        <div>{{res_add}}</div>
        <ul>
          <li v-for="(ev, index) in added_events" :key="index">
            {{ev.event.start}} -  {{ev.event.end}}  - 
            <span v-if="ev.suc==0" class="red">not</span> 
            added
          </li>
        </ul>
      </div>

      <button class="btn btn-auth" @click="add_event">Submit</button> 
      <button class="btn btn-auth" @click="cancel" type="cancel">Cancel</button>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'reservation',
  props: ['hour_format'],
  data () {
    return {
      room_id: 0,
      room_name: "",
      user_id: "",
      monthes_templ : [
        {"id":0, "name":"January"}, 
        {"id":1, "name":"February"},
        {"id":2, "name":"March"}, 
        {"id":3, "name":"April"}, 
        {"id":4, "name":"May"},
        {"id":5, "name":"June"}, 
        {"id":6, "name":"July"}, 
        {"id":7, "name":"August"}, 
        {"id":8, "name":"September"},
        {"id":9, "name":"October"},
        {"id":10, "name":"November"},
        {"id":11, "name":"December"}],
      users: [],
      year: 0,
      month: 0,
      day: 0,
      hour_start: 0,
      minute_start: 0,
      hour_end: 0,
      minute_end: 0,
      day_part_start: "am",
      day_part_end: "am",
      description: "",
      is_recur: 1,
      recur_period: "w",
      added_events: [],
      user: {},
      res_add: "",
      is_admin: false,
      duration: 1,
      err_desc: "",
      err_time: "",
      err_add: "",
    }
  },
  created(){
    var self = this
    if(localStorage['hash_log']){
      axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
        .then(function (response) {
          var user_id = parseInt(response.data.data.id) 
          if(user_id>0){
            self.user = {'id': response.data.data.id, 'fullname' :  response.data.data.fullname}
            self.user_id =  self.user.id
            if(response.data.data.role == "admin")
              self.is_admin = true
              
            if(self.is_admin) {
              self.getUsers()
            }else{
              self.users = [self.user]
            }

            self.room_id =  self.$route.params.room_id
            self.getRoomName()
            self.getCurParams()
          }else{
            self.$emit('logout')
          }
        }).catch(function (error) {
          self.$emit('logout')
        });
    }else{
      this.$emit('logout')
    }
  },
  methods:{
    getCurParams() {
      var now = new Date(); 
      this.year = now.getFullYear()
      var len = this.monthes_templ.length
      for(var i = 0; i < len; i++) {
        if(this.monthes_templ[i].id == now.getMonth()){
          this.month = this.monthes_templ[i].id
          break
        }
      }
      this.day = now.getDate()
      var monute_now = now.getMinutes()
      if(monute_now >10){
        this.hour_start = now.getHours()+1;
        this.minute_start = 0
        this.hour_end = this.hour_start;
        this.minute_end = 30
      }else {
        this.hour_start = now.getHours();
        this.minute_start = 30
        this.hour_end = this.hour_start+1;
        this.minute_end = 0
      }
    },

    getRoomName() {
      var self = this
      axios.get(serverUrl + 'BOOKER/client/api/room/'+this.room_id, this.config)
        .then(function (response) {
          self.room_name = response.data.data.name
        })
    },

    cancel(){
      this.description = this.err_add = this.res_add = ""
      this.is_recur = 1
      this.recur_period = "w"
      this.duration = 1
      this.added_events = []
      this.res_add = this.err_add = ""
      this.getCurParams()
    },

    getUsers(){
      var self = this
      axios.get(serverUrl+'BOOKER/client/api/user/', this.config)
        .then(function (response) {
          self.users = response.data.data
      })
      .catch(function (error) {
        //console.log(error);
      });
    },

    durationCheck: function(event){
      if(this.recur_period == "w" && (event.keyCode < 49 || event.keyCode > 52) 
                                  && (event.keyCode < 97 || event.keyCode > 100) )
      {
        event.preventDefault();
      }else if(this.recur_period == "b" && (event.keyCode < 49 || event.keyCode > 50) 
                                        && (event.keyCode < 97 || event.keyCode > 98))
      {
        event.preventDefault();
      }else if(this.recur_period == "m" && (event.keyCode != 49))
      {
        event.preventDefault();
      }
    },

    descriptionCheck: function(){
      var re = /^[!?\(\)=:.,;'"№%$\-\w\s]+$/
      if (re.test(this.description) !== true){
        this.err_desc= 'text contains invalid characters'
        return false
      }else {
        this.err_desc = ""
        return true
      }     
    },

    add_event(){
      if(!this.descriptionCheck()){
        this.err_add = "description contains invalid characters"
        return;
      }

      this.err_time = ""
      this.err_add = this.res_add = ""
      var unix_day_start = null
      var unix_day_end = null
   
      if(this.hour_format == 'h12'){
        if(this.day_part_start == "pm" && this.hour_start < 12){
          this.hour_start += 12
        }
        if(this.day_part_end == "pm" && this.hour_end < 12){
          this.hour_end += 12
        }
      }
      var  unix_time_start = this.getUnixTimestamp(this.year, this.month, this.day, this.hour_start, this.minute_start)
      var  unix_time_end = this.getUnixTimestamp(this.year, this.month, this.day, this.hour_end, this.minute_end)
      var unix_day_event = this.getUnixTimestamp(this.year, this.month, this.day, 0, 0)

      if(unix_time_start >= unix_time_end){
        this.err_time = "Uncorrect time. Time start must be less then time end and min time event = 30 minutes"
        var self = this
        setTimeout(function () {
          self.err_time = ""
        },2500);
      }else{
        var is_repeate_db = false, duration_db = null, recur_period_db = null
        if(this.is_recur == 1){
          is_repeate_db  = true
          recur_period_db = this.recur_period
          duration_db = this.duration
        }

        var data = new FormData()
        data.append("user_id", this.user_id)//this.user_id)
        data.append("room_id", this.room_id)
        data.append("description", this.description)
        data.append("start_event", unix_time_start)//start_event) 
        data.append("end_event", unix_time_end)//end_event)
        data.append("day_event", unix_day_event)//day_event)  
        data.append("is_repeate", is_repeate_db)  
        data.append("recur_period", recur_period_db)  
        data.append("duration", duration_db) 

        var self = this
        if(localStorage['hash_log']){
          axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
            .then(function (response) {
              var user_id = parseInt(response.data.data.id) 
              if(user_id>0){
                axios.post(serverUrl + 'BOOKER/client/api/event/', data, self.config)
                .then(function (response) {
                  if(response.data.err && response.data.err == true){
                    self.err_add = response.data.data
                    setTimeout(function () {
                      self.err_add = ""
                    },2500);
                  }else{
                    var count = parseInt(response.data.data.count)
                    if(count>0) {
                      self.added_events = response.data.data.events
                      self.res_add = count + " event(s) was(were) successfully added"
                      if(self.is_recur == true && count < ++self.duration_db){
                        self.res_add += "If the claimed armor is less than the added events - perhaps the room at this time is booked. Check on the calendar."
                      }
                    }else {
                      self.err_add = "sorry, there was a server error.Try again later"
                      setTimeout(function () {
                        self.err_add = ""
                      },2500);
                    }
                  }
                })
                .catch(function (error) {
                  self.err_add = "sorry, there was a server error.Try again later"
                });
              }else {
                self.$emit('logout')
              }
            }).catch(function (error) {
              self.$emit('logout')
            });
          }else {
            this.$emit('logout')
          }
      }
    },

    getUnixTimestamp : function(year, month, date, hours, minutes) {
      return Math.floor(new Date(year, month, date, hours, minutes, 0, 0).getTime() / 1000);
    },

  },
  computed:{
    years: function(){
      var now = new Date(); 
      var year_now = now.getFullYear()
      return [year_now, year_now+1]
    },

    monthes: function() {
      var now = new Date()
      var year_now = now.getFullYear() 
      var month_now = now.getMonth()
      if(this.year == year_now){
        var temp = []
        this.monthes_templ.forEach(function(month) {
          if(month.id >= month_now)
            temp.push(month)
        }, this);
        return temp
      }
      return this.monthes_templ
    },

    days: function() {
      var daysInCurrentMonth = 32- new Date(this.year,this.month , 32).getDate(); 
      var now = new Date(); 
      var m = now.getMonth()
      var y = now.getFullYear()
      var d = now.getDate()
      var days_temp = []
      for(var i = 1; i <= daysInCurrentMonth; i++) {
        if(m==this.month  && this.year==y && i<d)
          continue
        days_temp.push(i)
      }
      return days_temp
    },

    hours_12_end: function() {
      if(this.day_part_end=="am") {
          return hours_am
        }else {
          return hours_pm
        }
    },

    hours_12_start: function() {
      if(this.day_part_start=="am") {
        return hours_am
      }else {
        var temp = hours_pm.slice()
        temp.pop()
        return temp
      }
    },

    hours_24_start: function() {
      var temp = hours_24.slice()
      temp.pop()
      return temp
    },

    hours_24_end: function(){
      return hours_24
    },

    f12: function() {
      if(this.hour_format == 'h12') return true
      return false
    },

    f24: function() {
      if(this.hour_format == 'h24') return true
      return false
    },

    is_recurive_event: function() {
      if(this.is_recur=='1') return true
      return false
    },
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.reservation{
  margin-top: -70px;
}

.selected_room{
  margin-bottom: 20px;
}

.red{
  color: red;
}
</style>
