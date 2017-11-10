<template>
  <div class="employeeList">
    <router-link class="btn btn-auth"  :to="'/employeeList/registration'">
         Add user
    </router-link>
    <div class="row">
      <div  class="col-sm-6 col-md-6">
        <div class="err">{{err_del}}</div>
        <div>{{res_del}}</div>
        <table class="table table-bordered">
          <tr>
              <th>User fullname</th>
              <th>Edit</th>
              <th>Delete</th>
          </tr>
          <tr v-for="user in users" :key="user.id">
            <td><a href="mailto:user.email">{{user.fullname}}</a></td>
            <td>
              <router-link class="btn btn-auth"  :to="'/employeeList/editUser/'+ user.id">
                <span class='glyphicon glyphicon-wrench' aria-hidden='true'></span>
              </router-link>
            </td>
            <td>
              <button class='btn btn-auth' @click="deleteUser(user.id)">
                <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
              </button>
            </td>
          </tr>
        </table>
      </div>
      <div  class="col-sm-6 col-md-6">
        <router-view @updateEmployeeList='getUsers'></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'employeeList',
  data () {
    return {
      users: [],
      user: {},
      err_del: "",
      res_del: "",
      user_id: ""
    }
  },
  created(){
    var self = this
    if(localStorage['hash_log']){
    axios.get(serverUrl + 'BOOKER/client/api/user/'+localStorage['hash_log'], this.config)
      .then(function (response){
        var user_id = parseInt(response.data.data.id) 
        if(user_id>0){
          self.user = response.data.data
          if(self.user.role != 'admin') {
            self.$router.push({path: '/calendar'})
          }
            self.user_id = user_id
            self.getUsers()
        }else{
            self.$emit('logout')
        }
      }).catch(function (error) {
        self.$emit('logout')
      })
    }else{
      self.$emit('logout')
    }
  },
  methods:{
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

    deleteUser(user_id){
      var self = this
      if(user_id==this.user_id) {
        this.err_del = "you can not remove yourself"
        setTimeout(function () {
          self.err_del = ""
        },2500);
        return;
      }

      if (confirm("Are you sure you want to delete this user?")) {
        axios.delete(serverUrl+'BOOKER/client/api/user/' + user_id, this.config)
          .then(function (response) {
            if(response.data.err && response.data.err ==true){
              self.err_del = response.data['data']
              setTimeout(function () {
                self.err_del = ""
              },2500);
            }else{           
              if(parseInt(response.data.data)==1){
                self.res_del = "Record  deleted."
                setTimeout(function () {
                  self.res_del = ""
                },2500); 
                self.getUsers()
              }else{
                self.err_del = "Record doesn't deleted"
                setTimeout(function () {
                    self.err_del = ""
                },2500); 
              }
            }
          })
          .catch(function (error) {
            self.err_del = "Record doesn't deleted"
            setTimeout(function () {
              self.err_del = ""
            },2500); 
          });
       }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.employeeList{
  margin-top: -70px;
}

table {
  font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
  font-size: 14px;
  color: black;
  border-collapse: collapse;
  text-align: left;
  margin-top: 20px;
}

th {
  font-weight: normal;
  color: #66CABF;
  padding: 10px 12px;
}

td {
  background-image: url("/static/back.png");
  filter:alpha(opacity=60);
  opacity:0.6;
  color: #669;
  border-top: 1px solid white;
  padding: 10px 12px;
}

tr:hover td {
  background: transparent;
  color:#FFF;
}
</style>
