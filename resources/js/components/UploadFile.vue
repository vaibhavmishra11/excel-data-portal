<template>
  <v-app>
    <v-container>
      <v-btn @click="openFileInput" color="primary">Upload Files</v-btn>
      <v-btn @click="removeDuplicates" color="success"
        >Remove Duplicates and Download</v-btn
      >
      <v-btn @click="importFiles" color="info">Upload</v-btn>

      <input
        ref="fileInput"
        type="file"
        style="display: none"
        @change="handleFileChange"
        multiple
      />
    </v-container>
  </v-app>
</template>

<script>
export default {
  methods: {
    openFileInput() {
      this.$refs.fileInput.click();
    },
    handleFileChange(event) {
      const selectedFiles = event.target.files;
      console.log("Selected Files:", selectedFiles);
    },
    removeDuplicates() {
      console.log("Removing duplicates...");
    },
    importFiles() {
      console.log("Importing files...");
    },
  },
};
</script>

<style scoped>
</style>

