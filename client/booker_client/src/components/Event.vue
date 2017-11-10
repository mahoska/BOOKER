<template>
  <div class="event">
    <div v-if="is_not_active_user">
      This operation is currently unavailable. 
      Your authorization period may be expired on the site.
    </div>
    <div class="err_small">{{err_del}}</div>
    <div>{{res_del}}</div>
    <div v-if="!disactive">
     <div class="row">
      <div class="col-sm-12 col-md-12 ">
        <div class="selected_room">{{room}} BOOKED DETAILS</div>
      </div>
    </div>

    When: 
    <!--if 24 hour time format -->
    <div class="err_small ">{{err_time}}</div>
    <div class = "row" v-if="f24==true">
    <div  class="col-sm-2 col-md-2">
      <select v-model="hour_start" class="form-control select-event-time" >
        <option v-for="(hour_num, index) in hours_24_start" :value="hour_num" :key="index">
          {{hour_num}}
        </option>
      </select>
    </div>
    <div  class="col-sm-2 col-md-2">
      <select v-model="minute_start" class="form-control select-event-time" >
        <option  value="0">00</option>
        <option  value="30">30</option>
      </select>
    </div>
    <div  class="col-sm-1 col-md-1" style="text-align:center"> - </div>
      <div  class="col-sm-2 col-md-2">
        <select v-model="hour_end" class="form-control select-event-time">
          <option v-for="(hour_num, index) in hours_24_end" :value="hour_num" :key="index">
            {{hour_num}}
          </option>
        </select>
      </div>
      <div  class="col-sm-2 col-md-2">
        <select v-model="minute_end" class="form-control select-event-time" >
          <option  value="0">00</option>
          <option  value="30">30</option>
        </select>
      </div>
      <div  class="col-sm-3 col-md-3"></div>
    </div>

    <!--if 12 hour time format -->
    <div class = "row" v-if="f12==true">
      <div  class="col-sm-2 col-md-2">
        <select v-model="hour_start" class="form-control select-event-time" >
          <option v-for="hour_num in hours_12_start" :value="hour_num" :key="hour_num">
            {{hour_num}}
          </option>
        </select>
      </div>
      <div  class="col-sm-2 col-md-2">
        <select v-model="minute_start" class="form-control select-event-time">
          <option  value="0" >00</option>
          <option  value="30">30</option>
        </select>
      </div>
      <div class="col-sm-1 col-md-1">
        <select v-model="day_part_start" class="form-control select-event-time" >
          <option  value="am">AM</option>
          <option  value="pm">PM</option>
        </select>
      </div>
      <div class="col-sm-1 col-md-1" style="text-align:center"> - </div>
      <div class="col-sm-2 col-md-2">
        <select v-model="hour_end" class="form-control select-event-time">
          <option v-for="hour_num in hours_12_end" :value="hour_num" :key="hour_num">
            {{hour_num}}
          </option>
        </select>
      </div>
      <div  class="col-sm-2 col-md-2">
        <select v-model="minute_end" class="form-control select-event-time">
          <option  value="0">00</option>
          <option  value="30">30</option>
        </select>
      </div>
      <div class="col-sm-1 col-md-1">
        <select v-model="day_part_end" class="form-control select-event-time">
          <option  value="am">AM</option>
          <option  value="pm">PM</option>
        </select>
      </div>
      <div class="col-sm-1 col-md-1"></div>
    </div>

    Notes:
    <div class="row">
      <div class="col-sm-5 col-md-5">
        <textarea class="form-control" v-model="event.description" rows="5" 
                  @keydown="descriptionCheck" 
                  title="permissible symbols: letters of the Latin alphabet, numbers, and also !?()=: . , ; ' № % $ -">
        </textarea>
        <p class="err_small">{{err_desc}}</p>
      </div>
      <div  class="col-sm-7 col-md-7"></div>
    </div> 

    Who:
      <div class = "row">
      <div  class="col-sm-3 col-md-3">
        <select v-model="event.user_id" class="form-control">
          <option v-for="user in users" :value="user.id" :key="user.id">
            {{user.fullname}}
          </option>
        </select>
      </div>
      <div  class="col-sm-9 col-md-9"></div>
    </div>
      
    <div class="create">
      Submitted: {{event.data_create}}
    </div>

    <div class="checkbox" v-if="show_is_recurse">
      <label>
        <input type="checkbox"  v-model="is_recurse">Apply to occurrences?
      </label>
    </div>
    <!--result block-->
    <div>
      <div>{{res_upd}}</div>
      <ul>
        <li v-for="(ev, index) in updated_events" :key="index">
          {{ev.event.start}} -  {{ev.event.end}}  - <span v-if="ev.suc==0" class="red">not</span> updated
        </li>
      </ul>
    </div>
    <!--end result block-->
    <button class="btn btn-auth" @click="updateEvent" v-if="is_admin">Update</button> 
    <button class="btn btn-auth" @click="deleteEvent" v-if="is_admin">Delete</button>  
  </div>
</div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'event',
  data () {
    return {
      event_id: 0,
      hour_format: 'h12',
      show_info: true,
      user: {},
      users: [],
      event: {},
      hour_start: 0,
      minute_start: 0,
      hour_end: 0,
      minute_end: 0,
      day_part_start: "am",
      day_part_end: "am",
      is_recurse: false,
      show_is_recurse: false,
      disactive:false,
      is_not_active_user: false,
      updated_events: [],
      role:'user',
      is_admin: true,
      year: 0,
      month: 0,
      day: 0,
      err_time: "",
      err_desc: "",
      err_del: "",
      res_del: "",
      res_upd: "",
    }
  },
  created(){
    this.event_id =  this.$route.params.event_id
    this.hour_format =  this.$route.params.hour_format
    this.room =  this.$route.params.room
 
    var self = this
    //checking the validity time of the user's authorization
    if(localStorage['hash_log']){
      axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
        .then(function (response) {
          var user_id = parseInt(response.data.data.id) 
          if(user_id>0) {
            self.user = {'id': response.data.data.id, 'fullname' :  response.data.data.fullname}
            self.event = response.data.data
            self.role = response.data.data.role
            if(self.role == "admin") {
              self.getUsers()
            }
            //get information about event
            axios.get(serverUrl + 'BOOKER/client/api/event/'+self.event_id, self.config)
            .then(function (response) {
              self.event  = response.data.data
              var user = {"id": self.event.user_id, "fullname": self.event.fullname}
              if(self.role == "user") {
                self.users.push(user)
                if(user.id!=self.user.id){
                  self.is_admin = false
                }
              }

              var date_start = new Date(self.event.start_event*1000)
              self.year = date_start.getFullYear()
              self.month = date_start.getMonth()
              self.day = date_start.getDate()

              document.title = self.day+'.' + self.month+'.'+ self.year+'.'

              self.hour_start = date_start.getHours()
              self.minute_start = date_start.getMinutes()
                 
              var date_end = new Date(self.event.end_event*1000)
              self.hour_end=date_end.getHours()
              self.minute_end=date_end.getMinutes()

              if(self.hour_format == 'h12'){
                if(self.hour_start>12) {
                  self.hour_start-=12
                  self.day_part_start = "pm"
                }
                else if(self.hour_start==12){
                  self.day_part_start = "pm"
                }
                if(self.hour_end>12) {
                  self.hour_end-=12
                  self.day_part_end = "pm"
                }
                else if(self.hour_end==12){
                  self.day_part_end = "pm"
                }
              }

              if(parseInt(self.event.parent_event_id) || parseInt(self.event.is_repeat)>0)
                self.show_is_recurse = true

              }).catch(function (error){
                //console.log(error)
              });
          }else{
            self.disactive = true
            self.is_not_active_user=true
          }
        }).catch(function (error){
          self.disactive = true
          self.is_not_active_user=true
        });
    }else{
      this.disactive = true
      this.is_not_active_user=true
    }
  },
  methods:{
    updateEvent(){ 
      this.err_del = ""
      if(this.hour_format == 'h12'){
        if(this.day_part_start == "pm" && this.hour_start < 12){
          this.hour_start += 12
        }
        if(this.day_part_end == "pm" && this.hour_end < 12){
          this.hour_end += 12
        }
      }
      var  date = new Date(this.event.start_event*1000);
      var  unix_time_start = this.getUnixTimestamp(this.year, this.month, this.day,
                                                  this.hour_start, this.minute_start)
      var  unix_time_end = this.getUnixTimestamp(this.year, this.month, this.day, 
                                                this.hour_end, this.minute_end)
      var  unix_day_event = this.getUnixTimestamp(this.year, this.month, this.day, 0, 0)
      var unix_now =  Math.floor(new Date().getTime() / 1000);
      if(unix_time_start >= unix_time_end){
        this.err_time = "Uncorrect time. Time start must be less then time end and min time event = 30 minutes"
        var self = this
        setTimeout(function () {
          self.err_time = ""
        },2500);
        return;
      }else if(unix_now >= unix_time_start){
        this.err_time = "This event has already expired and is not subject to change"
        setTimeout(function () {
          self.err_time = ""
        },2500);
        return;
      }else if(!this.descriptionCheck()){
        this.err_del = "description contains invalid characters"
        return;
      }else{
        var self = this
        //check that user is active
        if(localStorage['hash_log']){
          axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
            .then(function (response) {
              var user_id = parseInt(response.data.data.id) 
              if(user_id>0){
                var recur = (self.is_recurse == true) ? 1 : 0
                axios.put(serverUrl + 'BOOKER/client/api/event/', 
                {
                  id: self.event_id,
                  description: self.event.description,
                  room_id: self.event.room_id,
                  user_id: self.event.user_id,
                  start_hour: self.hour_start,
                  end_hour: self.hour_end,
                  start_minute: self.minute_start,
                  end_minute: self.minute_end,
                  unix_start: unix_time_start,
                  unix_end: unix_time_end,
                  unix_day: unix_day_event,
                  is_recurs: recur
                }, self.config)
                .then(function (response) {
                  if(response.data.err && response.data.err ==true){
                    self.err_del = response.data['data']
                    setTimeout(function () {
                      self.err_del = ""
                    },3000);
                  }else{  
                    var count = parseInt(response.data.data.count);          
                    if(count>0){
                      self.updated_events = response.data.data.events
                      self.res_upd = count + " event(s) was(were) successfully updated" 
                    }else{
                      self.err_add = "sorry, there was a server error.Try again later"
                      setTimeout(function () {
                        self.err_add = ""
                      },2500);
                    }
                  }
                })
                .catch(function (error) {
                  self.err_del = "somthing wrong, no update was made. " +
                                "Perhaps you did not provide the data for the update"
                  setTimeout(function () {
                    self.err_del = ""
                  },2500);
                });
              }else{
                self.disactive = true
                self.is_not_active_user=true
              }
            }).catch(function (error){
              self.disactive = true
              self.is_not_active_user=true
            });
        }else{
          this.disactive = true
          this.is_not_active_user=true
        }
      }
    },

    deleteEvent(){
      if (confirm("Are you sure you want to delete this user?")) {
        var  date = new Date(this.event.start_event*1000);
        var  unix_time_start = this.getUnixTimestamp(this.year, this.month, this.day, this.hour_start, this.minute_start)
        var unix_now =  Math.floor(new Date().getTime() / 1000);  
        if(unix_now >= unix_time_start){
          this.err_time = "This event has already expired and is not subject to change"
          setTimeout(function () {
            this.err_time = ""
          },2500);
          return;
        }
        var self = this
        //check that user is active
        if(localStorage['hash_log']){
          axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
            .then(function (response) {
              var user_id = parseInt(response.data.data.id) 
              if(user_id>0){
                var recur = (self.is_recurse == true) ? 1 : 0
                axios.delete(serverUrl+'BOOKER/client/api/event/' + self.event_id +'/' + recur, self.config)
                  .then(function (response) {
                    if(response.data.err && response.data.err ==true){
                      self.err_del = response.data['data']
                      setTimeout(function () {
                        self.err_del = ""
                      },2500);
                    }else{  
                      var count = parseInt(response.data.data)         
                      if(count>0){
                        self.res_del = count + " record(s) was(were) deleted."
                        self.disactive = true
                        setTimeout(function () {
                          self.res_del = ""
                        },2500); 
                      }else{
                        self.err_del = "Record(s) doesn't deleted"
                        setTimeout(function () {
                          self.err_del = ""
                        },2500); 
                      }
                    }
                  }).catch(function (error) {
                      self.err_del = "Record doesn't deleted"
                      setTimeout(function () {
                        self.err_del = ""
                      },2500); 
                    });
              }else{
                self.disactive = true
              }
            }).catch(function (error){
              self.disactive = true
            });
        }else{
          this.disactive = true
        }
      }
    },

    getUnixTimestamp : function(year, month, date, hours, minutes) {
      //new Date(year, month, date, hours, minutes, seconds, ms)
      //get time  in second as in php (/1000)
      return Math.floor(new Date(year, month, date, hours, minutes, 0, 0).getTime() / 1000);
    },

    getUsers(){
      var self = this
      axios.get(serverUrl+'BOOKER/client/api/user/', this.config)
        .then(function (response) {
          self.users = response.data.data
      }).catch(function (error) {
        //console.log(error);
      });
    },

    descriptionCheck: function(){
      var re = /^[!?\(\)=:.,;'"№%$\-\w\s]+$/
      if (re.test(this.event.description) !== true){
        this.err_desc= 'text contains invalid characters'
        return false
      }else {
        this.err_desc = ""
        return true
      }     
    },
  }, 
  computed:{
    hours_12_end: function(){
      if(this.day_part_end=="am") {
            return hours_am
          }else {
            return hours_pm
          }
      },

      hours_12_start: function(){
        if(this.day_part_start=="am") {
          return hours_am
        }else{
          var temp = hours_pm.slice()
          temp.pop()
          return temp
        }
      },

      hours_24_start: function(){
        var temp = hours_24.slice()
        temp.pop()
        return temp
      },

      hours_24_end: function(){
        return hours_24
      },

      f12: function(){
        if(this.hour_format == 'h12') return true
        return false
      },

      f24: function(){
        if(this.hour_format == 'h24') return true
        return false
      },
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.event{
  position: relative;
  z-index: 1000;
  font-size:14px;
  margin-top: -100px;
}

.create{
  margin: 15px 0;
}

.select-event-time{
  width: 100px;
}


</style>