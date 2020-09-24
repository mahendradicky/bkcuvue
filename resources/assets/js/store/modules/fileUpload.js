import FILEUPLOADUAPI from '../../api/fileUpload.js';

export const fileUpload={
    namespaced: true,

     // state
  state: {
    data: {},
    failDataS: [], //collection
    count: {},
    dataStat: '',
    dataStatS: '',
    countStat: '',
    rules: [], //laravel rules
    options: [], //laravel options
    update: [], //update data
    updateStat: '',
  },

   // getters
   getters: {
    data: state => state.data,
    failDataS: state => state.failDataS,
    periode: state => state.periode,
    count: state => state.count,
    dataStat: state => state.dataStat,
    dataStatS: state => state.dataStatS,
    countStat: state => state.countStat,
    rules: state => state.rules,
    options: state => state.options,
    update: state => state.update,
    updateStat: state => state.updateStat,
  },

  actions: {
    store( {commit, state, dispatch}, [data] ){
        commit('setUpdateStat', 'loading');
        FILEUPLOADUAPI.store(data)
          .then( function( response ){
            if(response.data.saved){
              commit('setUpdate', response.data);
              commit('setUpdateStat', 'success');
            }else{
              commit('setUpdateStat', 'fail');
              commit('setFailDataS', response.data.file);
            }
          })
          .catch(error => {
            commit('setUpdate', error.response);   
            commit('setUpdateStat', 'fail');
          });
      },

      execute( {commit, state, dispatch}){
        // commit('setUpdateStat', 'loading');
        FILEUPLOADUAPI.execute()
          .then( function( response ){
            console.log(response.data)
    
          })
          .catch(error => {
            commit('setUpdate', error.response);   
            commit('setUpdateStat', 'fail');
          });
      },
  },

    // mutations
    mutations: {
        setData ( state, data ){
          state.data = data;
        },
        setFailDataS ( state, data ){
          state.failDataS.push(data);
        },
        resetFailDataS(state){
          state.failDataS = [];
        },
        setCount ( state, data ){
          state.count = data;
        },
        setDataStat( state, status ){
          state.dataStat = status;
        },
        setPeriodeStat( state, status ){
          state.periodeStat = status;
        },
        setDataStatS( state, status ){
          state.dataStatS = status;
        },
        setCountStat( state, status ){
          state.countStat = status;
        },
        setRules( state, rules ){
          state.rules = rules;
        },
        setOptions( state, options ){
          state.options = options;
        },
        setUpdate ( state, data ){
            state.update = data;
        },
        setUpdateStat( state,status ){
            state.updateStat = status;
        },
      }

}