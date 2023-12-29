
import './bootstrap';
import { createApp } from 'vue';
import vuetify from "./vuetify";



const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
import UploadFile from './components/UploadFile.vue';
import DashboardTable from './components/DashboardTable.vue';


app.component('example-component', ExampleComponent);
app.component('dashboard-table', DashboardTable);
app.component('upload-file',UploadFile);
app.use(vuetify);
app.mount('#app');
