<template>
    <div>
        <!-- header -->
		<page-header :title="title" :titleDesc="titleDesc" :titleIcon="titleIcon" :level="2" :level2Title="level2Title"
		 :level2Route="kelas" @level2Back="back()"></page-header>
        <fileUpload diskName = "local" pivotTableName="kegiatan_materi" path="aaaaa"></fileUpload>
    
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import pageHeader from "../../components/pageHeader.vue";
import appModal from '../../components/modal';
import Cleave from 'vue-cleave-component';
import fileUpload from '../../components/fileUpload';

export default {
    components: {
			pageHeader,
			appModal,
            Cleave,
            fileUpload,
		},
		data() {
			return {
				title: '',
				titleDesc: '',
				titleIcon: '',
				kelas: 'testUpload',
                level2Title: 'Test Upload',
                files:[],
				isUpload:false,
				checkMateri:[],
                showLoad:false,
                selectedItem: '',
				checkedMateri:[],
                selectedMateri:'',
                state: '',
				stat:'',
				cleaveOption: {
            number16: {
            numeral: true,
            numeralIntegerScale: 16,
            numeralDecimalScale: 0,
						stripLeadingZeroes: false,
						delimiter: ''
					},
				},
			}
		},

		created() {
		},
		mounted() {
			
		},

		computed: {
		...mapGetters('auth',{
				currentUser: 'currentUser'
			}),
		...mapGetters('fileUpload',{
				data :'data',
				updateResponse:'update',
				uploadStat : 'updateStat',
				failFiles : 'failDataS'
		})
	},
}
</script>