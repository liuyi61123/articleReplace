<template>
    <div class="card">
      <div class="card-header">
        测试
      </div>
      <div class="card-body">
          <p>{{message}}</p>
        <el-button type="primary" icon="el-icon-delete"></el-button>
      </div>
    </div>
</template>

<script>
    export default {
        props:['message'],
        data(){
            return {

            };
        },
        mounted() {
            console.log(this.message)
        }
    }
</script>
