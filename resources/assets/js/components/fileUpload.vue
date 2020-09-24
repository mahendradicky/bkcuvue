<template>
    <div>
        <form  @dragover.prevent @drop="OnDrop" action="#" class="dropzone dz-clickable" id="dropzone_multiple">
			<div class="container-fluid">
				<div class="row">
					<div  v-for="file in files">
						<div class="card" style="width: auto; " >
							<div class="card-body">
								<h5 class="card-title">{{file.name}}</h5>
								<div class="progress" v-show="showLoad">
									<div  class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
								</div>
								<a  v-if="stat!==''">{{stat}}</a>
								<a  v-if="stat==''" class="btn btn-primary" @click="removeFile(file)">Hapus</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div v-if="files.length==0" class="dz-default dz-message"><span>Drop files to upload</span>
			<label for="uploadbtn" style="font-size:15px;text-color:grey;"><span>or Click Here to Upload</span></label></div>
		</form>
		<div style="margin-bottom:2rem">
			<input id="uploadbtn" type="file" class="form-control" @change="upload" ref="fileInput" :multiple="true" style="display:none;margin-top: 0.5rem;">
			<input v-if="currentUser.id_cu===0" type="button" value="Upload" class="btn-primary align-middle" :disabled="files.length==0" @click="submitFiles">
		</div>
		
		<!-- modal -->
		<app-modal :show="modalShow" :state="modalState" :title="modalTitle" :content="modalContent" :size="modalSize" :color="modalColor"
		 @batal="modalTutup" @tutup="modalTutup" @confirmOk="modalConfirmOk" @successOk="modalTutup" @failOk="modalTutup"
		 @backgroundClick="modalBackgroundClick">

		 <!-- title -->
			<template slot="modal-title">
				{{ modalTitle }}
			</template>

		</app-modal>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import appModal from '../components/modal';
import { PusherAuth } from '../helpers/pusherauth.js';
import Echo from 'laravel-echo';
import Pusher from "pusher-js";
export default {
	components:{
		appModal
	},
	props:{
		diskName:{
			type: String,
			default: 'storage'
		},
		pivotTableName: {
			type: String
		},
		path:{
			type: String
		}
	},
	data() {
		return {
			files:[],
			stat:'',
			count:'',
			modalShow: false,
			modalState: '',
			modalTitle: '',
			modalColor: '',
			modalContent: '',
			modalSize: '',
			submited: false,
			state:'',
		}
	},

	watch: {
		uploadStat(value){

			if(value=='loading'){
				this.modalShow = true;
				this.modalState = value;
				this.modalColor = '';
			}
			
			if(value=='success'){
				if(this.count==this.files.length){
					if(this.failFiles.length<=0){
						this.modalTitle = 'Semua File Berhasil Diupload';
						this.files = this.failFiles;
						this.$store.dispatch('fileUpload/execute');
					}else{
						this.modalTitle = 'Beberapa File Gagal Diupload';
						this.files = this.failFiles;
					}
				}

				this.showLoad = false;
				this.modalShow = true;
				this.modalState = value;
				this.modalColor = '';
			}else if(value=='fail'){
				this.modalShow = true;
				this.modalState = value;
				this.modalColor = '';
				this.files = this.failFiles;
				this.stat = 'Gagal Diupload';
				this.showLoad = false;
			}
			
		}
	},
	mounted() {
		// PusherAuth();
		// 	window.Echo.private('KegiatanDestroyChannel.'+this.currentUser.id)
    	// 			.listen('KegiatanDestroy',(e) => {
		// 					console.log(e.pesan);
    	// 			});
	},
    methods: {
			modalOpen(state, isMobile, itemMobile) {
				this.modalShow = true;
				this.state = state;
				if (isMobile) {
					this.selectedItem = itemMobile;
				}
			},
	
			modalConfirmOk() {
				this.modalShow = false;
				if (this.state == 'hapusDiklat') {
					this.$store.dispatch(this.kelas + '/destroy', this.item.id);
				}else if (this.state == 'hapus') {
					this.$store.dispatch(this.kelas + '/destroyPeserta', this.selectedItem.id);
				}else if (this.state === 'hapusFile'){
					this.hapusFile(this.selectedMateri.id,this.selectedMateri.kegiatan_id,this.selectedMateri.file_name);
					this.state='hapusFile';
				}else if(this.state=='hapusAll'){
					this.hapusAll();
				}
			},
			modalTutup() {
				if(this.state == 'tambah' || this.state == 'ubah' || this.state == 'hapus' || this.state == 'batal'){
					this.tabName = 'peserta';
					this.fetchPeserta(this.query);
					this.fetchCountPeserta();
				}

				if(this.state == 'hapusDiklat'){
					this.back();
				}
				
				if(this.state == 'hapusFile'|| this.state == 'hapusAll'){
					this.$store.dispatch(this.kelas + '/indexMateri', this.$route.params.id);
				}

				if(this.isUpload===false && this.state!=='hapusFile'){
					this.files=[];
				}

				this.isDisableTablePeserta = false;
				this.modalShow = false;
			},
			modalBackgroundClick() {
				if (this.modalState === 'success') {
					this.modalTutup;
				} else if (this.modalState === 'loading') {
					// do nothing
				} else {
					this.modalShow = false
				}
				this.isDisableTablePeserta = false;
			},
      		OnDrop(e){
				e.preventDefault();
				e.stopPropagation();
				let droppedFiles = e.dataTransfer.files;
     			if(!droppedFiles) return;
      			([...droppedFiles]).forEach(f => {
				this.files.push(f);
				this.showLoad=false;
				this.stat='';
				});
			},

			upload(e) {
				this.showLoad=false;
				this.stat ='';
				let file = e.target.files || e.dataTransfer.files;
				if(!file.length) return;
				for (let i = 0; i < file.length; i++) {
					this.files.push(file[i]);
				}
			},

			clear(){
				const input = this.$refs.fileInput;
				input.type = 'text';
				input.type = 'file';
			},

			removeFile(file){
      			this.files = this.files.filter(f => {
        		return f != file;
				  });  
				console.log(this.path + this.diskName + this.pivotTableName);  
			},
				
			submitFiles(value) {
			this.$store.commit('fileUpload/resetFailDataS');
			var fileCount = this.files.length;
			for( let i = 0; i <fileCount; i++ ){
				this.count = i+1;
			if(this.files[i].id && i!==count) {
				continue;
			}
			this.$store.commit('fileUpload/setData', this.files[i]);
			let formData = new FormData();
			formData.append('file', this.data);
			formData.append('disk', this.diskName);
			formData.append('pivot', this.pivotTableName);
			
			this.$store.dispatch('fileUpload/store', [formData],{
			headers: {
				'Content-Type': 'multipart/form-data'
					}
				});

			}
			// console.log(this.data);
			// this.modalShow = true;
			// this.modalState = 'loading';
			// this.modalColor = '';
			// this.isUpload = true;
			this.showLoad=true;
			// this.state='loading';
	
			},
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