<template>
  <div class="add-client">
    <div class="row">
      <div  class="col-sm-1 col-md-1"></div>
      <div  class="col-sm-10 col-md-10">
        <div class="err" style="width: 100%">{{err_reg}}</div>
        <div  class="success err">{{is_reg}}</div>
        <div  class="form-group">
          <label for="fullname">Fullname</label>
          <input type="text" class="form-control" id="fullname"  placeholder="fullname" v-model.trim="fullname"  @blur="check_name">
          <div class="err_small" v-if="err_name">fullname contains invalid characters</div>
        </div>
        <div  class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email"  placeholder="email" v-model.trim="email" @blur="check_email">
          <div class="err_small" v-if="err_email">wrong email format</div>
        </div>
        <div  class="form-group">
          <label for="login">Login</label>
          <input type="text" class="form-control" id="login"  placeholder="login" v-model.trim="login" @blur="check_login">
          <div class="err_small">{{err_log}}</div>
        </div>
        <div  class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" placeholder="password" v-model.trim="password" >
        </div>
        <div class="form-group">
          <label>Role</label>
          <select v-model="role_id" class="form-control">
            <option v-for="role in roles" :value="role.id" :key="role.id">
              {{role.name}}
            </option>
          </select>
        </div>
        <div class="center">
          <button class="btn btn-auth" @click="addUser">Registration</button> 
          <button class="btn btn-auth" @click="cancel" type="cancel">Cancel</button>
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
      fullname: "",
      login: "",
      password: "",
      email: "",
      role_id: 2,
      roles: [],
      is_reg: "",
      err_reg: "",
      err_email: false,
      err_log: "",
      err_name : false,
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
    cancel() {
      this.login = this.password = this.fullname = this.email = this.role_id = ""
      this.err_reg = this.is_reg = this.err_log = this.err_name = this.err_email = ""
      this.$router.push({path: '/employeeList'})
    },

    check_email() {
      var re = /^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?(\.)[A-Za-z0-9]{2,3}$/
      if (re.test(this.email) != true) {
        this.err_email = true
        return false
      }else {
        this.err_email = false
        return true
      }
    },

    check_login() {
      var re = /^[\-_A-Za-z0-9]+$/
      if (re.test(this.login) !== true){
        this.err_log = 'login contains invalid characters'
        return false
      }else if (this.login.length < 6){
        this.err_log ='login is too short'
        return false
      }else {
        this.err_log = ""
        return true
      }     
    },

    check_name(){
      var re1 = /^[A-Za-z ]+$/
      if (re1.test(this.fullname) !== true){
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

    addUser(){
      this.is_reg = ""
			if(this.login=="" || this.password=="" || this.fullname=="" || this.email=="" || this.role_id==""){
        this.err_reg = "not all fields are filled";
			}else{
        if (this.check_email() && this.check_name() && this.check_login()) {
          this.err_reg = ""    
          var self = this
          var data = new FormData()
          data.append("login", this.login)
          data.append("password",this.password)
          data.append("fullname",this.fullname)
          data.append("email",this.email)
          data.append("role_id",this.role_id)  
          
          //check is user active or timeout session work
          var self = this
          if(localStorage['hash_log']){
            axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
              .then(function (response) {
                var client_id = parseInt(response.data.data.id) 
                if(client_id>0){
                  axios.post(serverUrl + 'BOOKER/client/api/user/', data, self.config)
                  .then(function (response) {
                    if(response.data.err && response.data.err ==true){
                      self.err_reg = response.data['data']
                      setTimeout(function () {
                        self.err_reg = ""
                      },2500);
                    }else{
                      if(parseInt(response.data.data)>0){
                        self.login = self.password = self.fullname  = self.email = self.role_id = ""
                        self.err_reg = ""
                        self.is_reg = "user is successfully added"
                         self.$emit('updateEmployeeList')
                        setTimeout(function () {
                          self.is_reg = ""
                        },2500);
                      }
                      else{
                        self.err_reg = "sorry, there was a server error.Try again later"
                        setTimeout(function () {
                          self.err_reg = ""
                        },2500);
                      }
                    }
                  })
                  .catch(function (error) {
                      self.err_reg = "sorry, there was a server error.Try again later"
                  });
                }else{
                  self.$emit('logout')
                }
            }).catch(function (error) {
              self.$emit('logout')
            });
          }else{
            this.$emit('logout')
          }
        }
      }
      setTimeout(function () {
        this.err_reg = ""
        this.is_reg = ""
      },2500);  
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.add-client{
  text-align:left;
  color: #FFF;
  margin-top: -40px;
}

.center{
  text-align: center;
}
</style>
