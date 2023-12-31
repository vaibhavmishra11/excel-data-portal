<template>
  <v-container>
    <v-row>
      <!-- Upload buttons -->
      <v-file-input label="Upload Excel Sheet" clearable @click:clear='clearFile' class="mr-4" variant="outlined"
        @change="handleFileChange"></v-file-input>

      <v-btn color="info" class="mr-4 mt-2" @click="removeDuplicateAndDownload" :disabled=disable>Remove Duplicates and
        Download</v-btn>
      <v-btn color="info" class="mt-2" @click="fetchClientData">Save Data</v-btn>
    </v-row>

    <!--fiters -->
    <v-row class="my-4">
      <v-col>
        <v-text-field v-model="email" label="Search by Email" variant="outlined"
          @keydown.enter="fetchData"></v-text-field>
      </v-col>
      <v-col>
        <v-text-field v-model="date" label="Search by Date" variant="outlined" @click="openDatePickerModal"
          append-icon="mdi-calendar">
          <v-dialog v-model="datePickerModal" width="290px">
            <v-date-picker v-model="date" scrollable>
              <div class="flex-grow-1 bottom:0"></div>
              <v-btn text color="primary" class="bottom" @click="closeDatePickerModal">Cancel</v-btn>
              <v-btn text color="primary" class="ml-4 bottom" @click="saveDate">OK</v-btn>
            </v-date-picker>
          </v-dialog>
        </v-text-field>
      </v-col>
      <v-col>
        <v-autocomplete v-model="uploader" label="Search by Uploader" variant="outlined" :items="['vaibhav', 'harsh']"
          clearable @keydown.enter="fetchData"></v-autocomplete>
      </v-col>

      <v-col class="d-flex gap-3">
        <v-btn @click="searchAll" color="info" class="mt-2 ml-16">Search</v-btn>

        <v-btn @click="clearSearch" color="info" class="mt-2">
          Clear Search</v-btn>
      </v-col>
    </v-row>

    <!-- Data table -->
    <v-data-table-server v-model:items-per-page="itemsPerPage" 
        :headers="headers" 
        :items-length="clientData.length"
        :items="clientData" 
        :loading="loading" 
        @update:page="handlePageUpdate"
        @update:items-per-page="handlePerPageUpdate"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        @update:sort-by="handleSortByUpdate"
        @update:sort-desc="handleSortDescUpdate"
        :items-per-page-options="[5, 10, 15, 20, -1]">
      <template #item.index="{ index }">
        {{ index + 1 }}
      </template>

    </v-data-table-server>
  </v-container>
</template>
  
<script>
export default {
  data() {
    return {
      date: null,
      email: "",
      uploader: "",
      datePickerModal: false,
      loading: false,
      file: null,
      disable: true,
      saveDisable: true,
      clientData: [],
      page:1,
      perPage:5,
      sortBy: ["created_at"],
      sortDesc: true,
      itemsPerPage: 5,
      currentPage: 1,

      headers: [
        { title: "S.No", key: "index", sortable: false},
        { title: "First Name", key: "first_name", sortable: true },
        { title: "Last Name", key: "last_name", sortable: true },
        { title: "Email", key: "email", sortable: true },
        { title: "Uploader", key: "uploader", sortable: true },
        { title: "Uploaded On", key: "created_at", sortable: true },
      ],
    };
  },
  mounted() {
    this.fetchClientData();

  },

  methods: {
    clearFile() {
      console.log('called')
      this.file = null;
    },
    handleFileChange(event) {
      this.file = event.target.files[0];
      this.disable = false;

    },

    async removeDuplicateAndDownload() {
      if (!this.file) {
        alert("Please select a file");
        return;
      }
      const formData = new FormData();
      formData.append("file", this.file);
      try {
        const response = await axios.post("/api/remove-download", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
          responseType: "blob",
        });
        const blob = new Blob([response.data], {
          type: "application/octet-stream",
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = "new_excel_file.xlsx";
        link.click();
        this.saveDisable = false;

      } catch (error) {
        console.error("Error", error.message);
        this.file = null;
      } finally {
        this.clearFile();
      }
    },

    saveData() {
      if (!this.file) {
        alert("Please select a file");
        return;
      }
      this.handleSaveData(this.file);
    },
    async handleSaveData(file) {
      const formData = new FormData();
      formData.append("file", file);
      await axios.post("/api/save-data", formData)
        .then((response) => {
          console.log("File uploaded successfully");
          alert("file uploaded successfully");
          this.clearFile();
        })
        .catch((error) => {
          console.error("Error uploading file", error);
        });
    },

    fetchData() {
      const filteredData = this.serverItems.filter((item) =>
        this.matchesSearchCriteria(item)
      );
      this.serverItems = filteredData;
      this.totalItems = filteredData.length;
    },

    matchesSearchCriteria(item) {
      const emailMatch = item.email.includes(this.email);
      const dateMatch = this.date ? item.uploaded_on === this.date : true;
      const uploaderMatch = item.uploader.includes(this.uploader);

      return emailMatch && dateMatch && uploaderMatch;
    },
    openDatePickerModal() {
      this.datePickerModal = true;
    },

    closeDatePickerModal() {
      this.datePickerModal = false;
    },

    saveDate() {
      this.closeDatePickerModal();
    },

    searchAll() {
      this.fetchData();
    },

    clearSearch() {
      this.email = "";
      this.date = "";
      this.uploader = "";
      this.fetchData();
    },

    async fetchClientData(sortBy) {
      try {
        // const { data } = await axios.get("/api/fetch-data");
        const { data } = await axios.get(`/api/fetch-data?page=${this.page}&perPage=${this.perPage}&desc=${this.sortDesc}&sortBy=${this.sortBy}`);
        console.log(data);
        this.clientData = data.data;
      } catch (error) {
        console.error("Error in fetching data", error);

      }
    },
    handlePageUpdate(page) {
      this.page = page;
      this.fetchClientData();
    },
    handlePerPageUpdate(perPage) {
      this.perPage = perPage;
      this.fetchClientData();
    },
    handleSortByUpdate(sortBy) {
      console.log(sortBy);
      this.sortBy = [sortBy[0].key];
      
      this.fetchClientData();
    },
    handleSortDescUpdate(sortDesc) {
      console.log(sortDesc);
      this.sortDesc= [sortBy[0].order];
      
      this.fetchClientData();
    },
  },
};
</script>
  