export default{
    store: function (data){
        return axios.post('/api/fileUpload/store', data);
      },
    execute: function(){
        return axios.post('/api/fileUpload/execute');
    }
}