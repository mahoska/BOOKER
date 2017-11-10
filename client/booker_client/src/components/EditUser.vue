<template>
  <div class="edit-user">
    <div class="row">
      <div  class="col-sm-1 col-md-1"></div>
      <div  class="col-sm-10 col-md-10">
        <div class="err" style="width: 100%">{{err_upd}}</div>
        <div  class="err success">{{is_upd}}</div>
        <div class="lbl_upd">User(id:{{user_id}}) edit form:</div>
        <div  class="form-group">
          <label for="fullname">Fullname</label>
          <input type="text" class="form-control" id="fullname"  placeholder="fullname" v-model.trim="user.fullname"  @blur="check_name">
          <div class="err_small" v-if="err_name">fullname contains invalid characters</div>
        </div>
        <div  class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email"  placeholder="email" v-model.trim="user.email" @blur="check_email">
          <div class="err_small" v-if="err_email">wrong email format</div>
        </div>
        <div  class="form-group">
          <label for="login">Login</label>
          <input type="text" class="form-control" id="login"  placeholder="login" v-model.trim="user.login" @blur="check_login">
          <div class="err_small">{{err_log}}</div>
        </div>
        <div  class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" placeholder="password" v-model.trim="user.password" >
        </div>
        <div class="form-group">
          <label>Role</label>
          <select v-model="user.role_id" class="form-control">
            <option v-for="role in roles" :value="role.id" :key="role.id">
              {{role.name}}
            </option>
          </select>
        </div>
        <div class="center">
          <button class="btn btn-auth" @click="editUser">Update user</button> 
          <button class="btn btn-auth" @click="cancel" type="cancel">cancel</button>
        </div>
      </div>
      <div  class="col-sm-1 col-md-1"></div>  
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'registration',
  props: ['hour_format'],
  data () {
    return {
      user: {},
      roles: [],
      is_upd: "",
      err_upd: "",
      err_email: false,
      err_log: "",
      err_name : false,
      user_show: true
    }
  },
  created(){
    var self = this
    //check is user active or timeout session work and check is admin?
    if(localStorage['hash_log']){
      axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
        .then(function (response) {
          var user_id = parseInt(response.data.data.id) 
          if(user_id>0){
            if(response.data.data.role == "admin"){
              self.getRoles()
            }else{   
               self.$router.push({path: '/calendar'})
            }
          }else{   
              self.$emit('logout')
          }
      }).catch(function (error){
        self.$emit('logout')
      });
    }else{
       this.$emit('logout')
    }
      this.getRoles();
  },
  methods:{
    editUser(){
      this.is_upd = ""
			if(this.login=="" || this.password=="" || this.fullname=="" || this.email=="" || this.role_id==""){
        this.err_upd = "not all fields are filled";
			}else{
        if (this.check_email() && this.check_name() && this.check_login()) {
          this.err_upd = ""    
          var self = this
          axios.put(serverUrl + 'BOOKER/client/api/user/', 
          {
            edit: true,
            fullname: this.user.fullname,
            login: this.user.login,
            password: this.user.password,
            email: this.user.email,
            role_id: this.user.role_id,
            id: this.user_id
          }, this.config)
        .then(function (response) {
          if(response.data.err && response.data.err ==true){
            self.err_upd = response.data['data']
            setTimeout(function () {
              self.err_upd = ""
            },3000);
          }else{  
           if(response.data.data == '1'){
              self.is_upd = "record was updeted."
              setTimeout(function () {
                self.is_upd = ""
              },2500);
              self.$emit('updateEmployeeList')
           }
          }
        })
        .catch(function (error) {
            self.is_upd = "No update was made. No new data was provided for the changes"
            setTimeout(function () {
              self.is_upd = ""
            },2500);
        });
      }
      setTimeout(function () {
        this.err_upd = this.is_upd = ""
      },2500);  
    }
  },
   
  cancel(){
    this.login = this.password = this.fullname = this.email = this.role_id = ""
    this.err_reg = this.is_reg = this.err_log = this.err_name = this.err_email = ""
    this.$router.push({path: '/employeeList'})
  },

  check_email() {
    var re = /^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?(\.)[A-Za-z0-9]{2,3}$/
    if (re.test(this.user.email) != true) {
      this.err_email = true
      return false
    }else {
      this.err_email = false
      return true
    }
  },

  check_login(){
    var re = /^[\-_A-Za-z0-9]+$/
    if (re.test(this.user.login) !== true){
      this.err_log = 'login contains invalid characters'
      return false
    }else if (this.user.login.length < 6){
      this.err_log ='login is too short'
      return false
    }else {
      this.err_log = ""
      return true
    }     
  },

  check_name(){
    var re1 = /^[A-Za-z ]+$/
    if (re1.test(this.user.fullname) !== true){
      this.err_name = true
      return false
    }else {
      this.err_name = false
      return true
    }
  },

  getRoles(){
    var self = this
    axios.get(serverUrl+'BOOKER/client/api/role/', this.config)
      .then(function (response) {
        self.roles = response.data.data
    })
    .catch(function (error) {
      //console.log(error);
    });
  },

  }, 
computed:{
  user_id: function(){
    this.user_show = true
    var id = this.$route.params.user_id
    var self = this
    axios.get(serverUrl + 'BOOKER/client/api/user/'+ id, this.config)
      .then(function (response) {
        self.user = response.data.data
        self.user.password = ""
        if(self.user.length == 0){
          self.err_upd = "Information about this client is absent"
          self.user_show = false
        }
      })
      .catch(function (error) {
        self.err_upd = "Information about this client is absent"
        self.user_show = false
      }); 
    return id;
  }
}

}
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.edit-user{
  margin-top: -50px;
}

.center{
  text-align: center;
}

.lbl_upd{
  text-align:right;
  font-weight:bold;
  color:#3231b9;
  text-decoration:underline;
}
</style>
