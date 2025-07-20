<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-overlay :absolute="absolute" :value="overlay">
        <v-progress-circular
          :size="70"
          :width="7"
          color="primary"
          indeterminate
        ></v-progress-circular>
      </v-overlay>
      <v-main>
        <v-breadcrumbs :items="items">
          <template v-slot:item="{ item }">
            <v-breadcrumbs-item :to="item.link" :disabled="item.disabled">
              {{ item.text.toUpperCase() }}
            </v-breadcrumbs-item>
          </template>
        </v-breadcrumbs>
        <v-data-table
          :headers="headers"
          :items="exams"
          :search="search"
          :loading="loading"
          loading-text="Loading... Please wait"
          class="elevation-8"
          v-if="hasPermission('exam-list')"
        >
          <template v-slot:top>
            <v-toolbar flat class="my-2 pt-2">
              <v-toolbar-title class="mt-4">Exam Lists </v-toolbar-title>
              <v-divider vertical class="ma-2 ml-4" thickness="20px"></v-divider>
              <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn 
                    small 
                    class="mx-2 mt-4" 
                    color="primary" 
                    rounded 
                    fab
                    v-bind="attrs" v-on="on"
                    @click="clear() + (dialog = true)"
                  >
                      <v-icon>mdi-plus</v-icon> 
                  </v-btn>
                </template>
                <span>Add Data</span>
              </v-tooltip>
              <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn 
                    small 
                    class="mr-2 mt-4 white--text" 
                    color="#AB47BC" 
                    rounded 
                    fab 
                    @click="getExam()"
                    :disabled="loading"
                    v-bind="attrs" v-on="on"
                  > 
                    <v-icon>mdi-refresh</v-icon> 
                  </v-btn>
                </template>
                <span>Refresh Data</span>
              </v-tooltip> 
              <v-spacer></v-spacer>
              <v-text-field
                v-model="search"
                label="Search"
                single-line
                hide-details=""
                append-icon="mdi-magnify"
              >
              </v-text-field>
            </v-toolbar>
          </template>
          <template v-slot:item.active="{ item }">
            <v-chip :color="item.active ? 'success' : 'secondary'"> {{ item.active ? 'Active' : 'Inactive' }} </v-chip>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-tooltip top v-if="hasPermission('exam-edit')">
              <template v-slot:activator="{ on, attrs }">
                <v-icon
                  color="info"
                  @click="examQuestionnaire(item)"
                  v-bind="attrs" 
                  v-on="on"
                >
                  mdi-playlist-plus
                </v-icon>
              </template>
              <span>Questionnaire</span>
            </v-tooltip>
            <v-tooltip top v-if="hasPermission('exam-edit')">
              <template v-slot:activator="{ on, attrs }">
                <v-icon
                  small
                  class="mx-1"
                  color="green"
                  @click="editExam(item)"
                  v-bind="attrs" 
                  v-on="on"
                >
                  mdi-pencil
                </v-icon>
              </template>
              <span>Edit</span>
            </v-tooltip>
            <v-tooltip top v-if="hasPermission('exam-edit')">
              <template v-slot:activator="{ on, attrs }">
                <v-icon
                  small
                  color="red"
                  @click="showConfirmAlert(item, 'delete-exam')"
                  v-if="hasPermission('exam-delete')"
                  v-bind="attrs" 
                  v-on="on"
                >
                  mdi-delete
                </v-icon>
              </template>
              <span>Delete</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <v-dialog v-model="dialog" max-width="500" persistent>
          <v-card>
            <v-card-title class="mb-0 pb-2 primary">
              <span class="headline text-white">{{ formTitle }}</span>
            </v-card-title>
            <v-divider class="mt-0"></v-divider>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      name="title"
                      v-model="editedItem.title"
                      :error-messages="titleErrors + examError.title"
                      label="Title"
                      @input="$v.editedItem.title.$touch() + (examError.title = [])"
                      @blur="$v.editedItem.title.$touch()"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      name="description"
                      v-model="editedItem.description"
                      :error-messages="descriptionErrors + examError.description"
                      label="Description"
                      @input="$v.editedItem.description.$touch() + (examError.description = [])"
                      @blur="$v.editedItem.description.$touch()"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      name="passing_score"
                      v-model="editedItem.passing_score"
                      label="Passing Score"
                      :error-messages="passingScoreErrors"
                      @input="$v.editedItem.passing_score.$touch()"
                      @blur="$v.editedItem.passing_score.$touch()"
                      @keypress="intNumValFilter()"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="2" class="my-0 py-0">
                    <v-switch
                      v-model="editedItem.active"
                      hide-details
                      inset
                    >
                      <template v-slot:label>
                        <v-chip :color="editedItem.active ? 'success' : ''" class="mt-1">{{activeStatus}}</v-chip>
                      </template>
                    </v-switch>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-divider class="mb-3 mt-0"></v-divider>
            <v-card-actions class="pa-0">
              <v-spacer></v-spacer>
              <v-btn color="#E0E0E0" @click="close" class="mb-3 mr-1">
                Cancel
              </v-btn>
              <v-btn
                color="primary"
                @click="save"
                :disabled="disabled"
                class="mb-3 mr-4"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialog_questionnaire" persistent fullscreen>
          <v-card>
            <v-card-title class="mb-0 py-2 primary">
              <span class="headline text-white">{{ 'Questionnaire - ' + editedItem.title }}</span>
              <v-spacer></v-spacer>
              <v-btn @click="closeQuestion()" icon dark>
                <v-icon> mdi-close-circle </v-icon>
              </v-btn>
            </v-card-title>
            <!-- <v-divider class="mt-0"></v-divider> -->
            <v-card-text class="full-height-questionnaire-card">
              <v-row class="mt-2 px-0">
                <v-col cols="4" class="ma-0 pl-0 pr-2">
                  <v-card class="full-height-questionnaire-details-card pa-4 ma-0 mt-0">
                    <v-row>
                      <v-col>
                        <span class="h3 font-weight-bold mt-2"> Question Details</span>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col class="my-0 py-0">
                        <v-autocomplete
                          outlined
                          dense
                          name="type"
                          :items="exam_types"
                          v-model="editedQuestion.type"
                          :error-messages="examTypeErrors + questionError.type"
                          label="Type"
                          @input="$v.editedQuestion.type.$touch() + (questionError.type = [])"
                          @blur="$v.editedQuestion.type.$touch()"
                        ></v-autocomplete>
                      </v-col>
                      <v-col class="my-0 py-0">
                        <v-text-field
                          outlined
                          dense
                          name="points"
                          v-model="editedQuestion.points"
                          label="Points"
                          :error-messages="pointsErrors"
                          @input="$v.editedQuestion.points.$touch()"
                          @blur="$v.editedQuestion.points.$touch()"
                          @keypress="intNumValFilter()"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row class="mt-6">
                      <v-col class="my-0 py-0">
                        <v-textarea
                          name="question"
                          v-model="editedQuestion.question_text"
                          :error-messages="questionTextErrors + questionError.question_text"
                          label="Question Text"
                          @input="$v.editedQuestion.question_text.$touch() + (questionError.question_text = [])"
                          @blur="$v.editedQuestion.question_text.$touch()"
                          rows="2"
                          outlined
                        ></v-textarea>
                      </v-col>
                    </v-row>
                    <template v-if="editedQuestion.type == 'Multiple Choice'">
                      <v-divider style="border: 0.5px solid; border-radius: 1px;" class="elevation-6"></v-divider>
                      <v-row>
                        <v-col>
                          <span class="h3 font-weight-bold mt-2">Choices</span>
                          <v-tooltip top>
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn 
                                x-small 
                                class="ml-2 mb-3"
                                color="primary" 
                                rounded 
                                fab
                                v-bind="attrs" v-on="on"
                                @click="addChoiceItems()"
                              >
                                  <v-icon>mdi-plus</v-icon> 
                              </v-btn>
                            </template>
                            <span>Add Choice</span>
                          </v-tooltip>
                        </v-col>
                      </v-row>
                      <template v-for="(choice, i) in editedQuestion.choices">
                        <v-row>
                          <v-col class="my-0 py-0">
                            <v-text-field
                              class="mb-1"
                              outlined
                              dense
                              name="points"
                              v-model="editedQuestion.choices[i]"
                              label="Points"
                              hide-details=""
                            >
                            <template slot="append-outer">
                              <v-btn small color="red" icon>
                                <v-icon @click="">mdi-delete</v-icon>
                              </v-btn>
                            </template>
                            </v-text-field>
                          </v-col>
                        </v-row>
                      </template>
                    </template>
                    <v-divider style="border: 0.5px solid; border-radius: 1px;" class="elevation-6 mt-6"></v-divider>
                    <v-row>
                      <v-col>
                        <span class="h3 font-weight-bold mt-2">Answer</span>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col class="my-0 py-0">
                        <v-text-field
                          outlined
                          dense
                          name="points"
                          v-model="editedQuestion.points"
                          label="Points"
                          :error-messages="pointsErrors"
                          @input="$v.editedQuestion.points.$touch()"
                          @blur="$v.editedQuestion.points.$touch()"
                          @keypress="intNumValFilter()"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col align="right">
                        <v-btn small class="mr-1" @click="clearQuestion()">Cancel</v-btn> 
                        <v-btn small color="primary" @click="saveQuestionnaire()">{{ editedQuestionIndex > -1 ? 'Update' : 'Add' }}</v-btn>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-col>
                <!-- <v-divider vertical style="border: 0.5px solid; border-radius: 1px;" class="elevation-6"></v-divider> -->
                <v-col cols="8">
                  <v-row>
                    <v-col class="my-0 pl-2">
                      <v-card class="pt-2">
                        <v-simple-table dense >
                          <template v-slot:default>
                            <thead>
                              <tr>
                                <th class="pl-2-" style="width:6%">#</th>
                                <th class="text-left" style="width:15%">Type</th>
                                <th class="text-left" style="width:63%">Questions</th>
                                <th class="text-left" style="width:7%">Points</th>
                                <th class="text-left" style="width:9%">Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(question, index) in exam_questions">
                                <td style="width:6%"> {{ index + 1 }} </td>
                                <td style="width:15%"> {{ question.type }} </td>
                                <td style="width:63%" class="ellipsis"> {{ question.question_text }} asdasdasdas dasdsqwerrthfgnfgh asdasdqwq sdg wetwer asds r sdfawew aeaw eawe    </td>
                                <td style="width:7%"> {{ question.points }} </td>
                                <td style="width:9%">
                                  <v-tooltip top v-if="hasPermission('exam-edit')">
                                    <template v-slot:activator="{ on, attrs }">
                                      <v-icon
                                        small
                                        class="mx-1"
                                        color="green"
                                        @click="editQuestion(question)"
                                        v-bind="attrs" 
                                        v-on="on"
                                      >
                                        mdi-pencil
                                      </v-icon>
                                    </template>
                                    <span>Edit</span>
                                  </v-tooltip>
                                  <v-tooltip top v-if="hasPermission('exam-edit')">
                                    <template v-slot:activator="{ on, attrs }">
                                      <v-icon
                                        small
                                        color="red"
                                        @click="showConfirmAlert(question, 'delete-qustion')"
                                        v-if="hasPermission('exam-delete')"
                                        v-bind="attrs" 
                                        v-on="on"
                                      >
                                        mdi-delete
                                      </v-icon>
                                    </template>
                                    <span>Delete</span>
                                  </v-tooltip>
                                </td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
            <!-- <v-divider class="mb-3 mt-0"></v-divider>
            <v-card-actions class="pa-0">
              <v-spacer></v-spacer>
              <v-btn color="#E0E0E0" @click="closeQuestion" class="mb-3 mr-1">
                Cancel
              </v-btn>
              <v-btn
                color="primary"
                @click="saveQuestionnaire"
                :disabled="disabled"
                class="mb-3 mr-4"
              >
                Save
              </v-btn>
            </v-card-actions> -->
          </v-card>
        </v-dialog>
      </v-main>
    </div>
  </div>
</template>
<style scoped>
  .full-height-questionnaire-card {
    height: calc(110vh - 135px); /* Adjust 270px to suits your needs */
    overflow-y: auto;
    overflow-x: hidden;
  }

  .full-height-questionnaire-details-card {
    height: calc(104vh - 135px); /* Adjust 270px to suits your needs */
    overflow-y: auto;
    overflow-x: hidden;
  }

  table {
    width: 100%;
  }

  thead, tbody, tr, td, th { display: block; }

  tr:after {
      content: ' ';
      display: block;
      visibility: hidden;
      clear: both;
  }

  tbody {
      height: calc(99vh - 135px);
      overflow-y: auto;
  }

  tbody td, thead th {
      float: left;
  }

  .ellipsis { 
    position: relative; 
  } 
  .ellipsis:before { 
    content: ' '; 
    visibility: hidden; 
  } 
  .ellipsis { 
    /* position: absolute;  */
    left: 0; 
    right: 0; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis; 
  } 

</style>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import {
  required,
  maxLength,
  email,
  minLength,
  sameAs,
} from "vuelidate/lib/validators";

import { mapGetters, mapState } from 'vuex';  

export default {

  mixins: [validationMixin],

  validations: {
    editedItem: {
      title: { required },
      description: { required },
      passing_score: { required },
    },
    editedQuestion: {
      exam_id: { required },
      type: { required },
      question_text: { required },
      answer: { required },
      points: { required },
    },
  },
  data() {
    return {
      absolute: true,
      overlay: false,
      items: [
        {
          text: "Home",
          disabled: false,
          link: "/dashboard",
        },
        {
          text: "exams Record",
          disabled: true,
        },
      ],
      search: "",
      headers: [
        { text: "Title", value: "title" },
        { text: "Description", value: "description" },
        { text: "Status", value: "active" },
        { text: "Actions", value: "actions", sortable: false, width: "110px" },
      ],
      expanded: [],
      disabled: false,
      dialog: false,
      dialogPermission: false,
      exams: [],
      editedIndex: -1,
      editedItem: {
        title: "",
        description: "",
        passing_score: "",
        active: 1,
      },
      defaultItem: {
        title: "",
        description: "",
        passing_score: "",
        active: 1,
      },
      loading: true,
      examError: {
        title: [],
        description: [],
        passing_score: [],
        active: [],
      },
      exam_questions: [
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
        { type: 'Multiple Choice', question_text: 'asdadasd', points: 1 },
      ],
      editedQuestion: {
        exam_id: "",
        type: "",
        question_text: "",
        points: "",
        answer: "",
        choices: [''],
      },
      defaultQuestion: {
        exam_id: "",
        type: "",
        question_text: "",
        points: "",
        answer: "",
        choices: [''],
      },
      questionError: {
        type: [],
        question_text: [],
        points: [],
        question: [],
      },
      editedQuestionIndex: -1,
      dialog_questionnaire: false,
      exam_types: ['Multiple Choice', 'Fill-in the blanks', 'Enumeration', 'Essay'],
      choiceItems: [],
    };
  },

  methods: {
    getExam() {
      this.loading = true;
      axios.get("/api/exam/index").then(
        (response) => {
          console.log(response.data);
          
          this.exams = response.data.exams;
          this.loading = false;
        },
        (error) => {
          console.log(error);
          
          this.isUnauthorized(error);
        }
      );
    },

    editExam(item) {
      this.editedIndex = this.exams.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteExam(exam_id) {
      const data = { exam_id: exam_id };

      axios.post("/api/exam/delete", data).then(
        (response) => {
          if (response.data.success) {
            // send data to Sockot.IO Server
            this.$socket.emit("sendData", { action: "exam-delete" });
          }
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    showAlert(msg, icon) {
      this.$swal({
        position: "center",
        icon: icon,
        title: msg,
        showConfirmButton: false,
        timer: 2500,
      });
    },

    showConfirmAlert(item) {
      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete record!",
      }).then((result) => {
        // <--

        if (result.value) {
          // <-- if confirmed

          const exam_id = item.id;
          const index = this.exams.indexOf(item);

          //Call delete User function
          this.deleteExam(exam_id);

          //Remove item from array services
          this.exams.splice(index, 1);

          this.$swal({
            position: "center",
            icon: "success",
            title: "Record has been deleted",
            showConfirmButton: false,
            timer: 2500,
          });
        }
      });
    },

    close() {
      this.dialog = false;
      this.clear();
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      // this.$v.$touch();

      if (!this.$v.error) {
        
        this.disabled = true;
        this.overlay = true;

        const exam_id = this.editedItem.id;
        let url = "/api/exam" + (this.editedIndex > -1 ? "/update" : "/store");    
        let data = {
          title: 'MPQ',
          description: 'Multidimensional Personality Questionnaire',
          active: 1,
          passing_score: 5,
          // exam_questions: [
          //   {
          //     type: 'Multiple Choice',
          //     question_text: 'Select the PHP Framework',
          //     choices: [
          //       'VueJS',
          //       'ReactJS',
          //       'Laravel',
          //       'Python',
          //       'HTML',
          //     ],
          //     points: 1,
          //     answer: 'Laravel',
          //   },
          //   {
          //     type: 'Fill-In The Blanks',
          //     question_text: 'Laravel is a [blank] Framework',
          //     choices: [],
          //     points: 1,
          //     answer: 'PHP',
          //   },
          //   {
          //     type: 'Enumeration',
          //     question_text: 'Enumerate atleast 3 PHP Frameworks',
          //     choices: [],
          //     points: 3,
          //     answer: 'Laravel,CodeIgniter,CakePHP,Phalcon,Yii,Laminas,FuelPHP,Symfony,Fat-Free Framework',
          //   },
          //   {
          //     type: 'Essay',
          //     question_text: 'What is the importance of PHP Framework',
          //     choices: [],
          //     points: 10,
          //     answer: '',
          //   }
          // ],
        };

        axios.post(url, this.editedItem).then(
          (response) => {
            
            this.overlay = false;
            this.disabled = false;
            let data = response.data;
            
            console.log(data);
            
            if (data.success) {
              // send data to Sockot.IO Server
              // this.$socket.emit("sendData", { action: "exam-master-data-edit" });

              if(this.editedIndex > -1)
              {
                Object.assign(this.exams[this.editedIndex], data.exam);          
              }
              else
              {
                this.exams.push(data.exam);
              }

              this.showAlert(data.success, "success");
              this.close();
            } else {
                let errors = response.data;
                let errorNames = Object.keys(response.data);

                errorNames.forEach(value => {
                  this.examError[value].push(errors[value]);
                });
              }
          },
          (error) => {
            this.isUnauthorized(error);

            this.overlay = false;
            this.disabled = false;
          }
        );
      }
    },

    saveQuestionnaire() {
      
      this.$v.editedQuestion.$touch();

      if(this.$v.editedQuestion.$error)
      {
        let data = {};
        axios.post('/api/exam_question/store', data).then(
          (response) => {
            let data = response.data;
            console.log(data);
            
          },
          (error) => {

          }
        )
      }
      
    },

    examQuestionnaire(item) {
      this.editedIndex = this.exams.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog_questionnaire = true;
    },

    editQuestion(item) {
      this.editedQuestionIndex = this.exam_questions.indexOf(item);
      this.editedQuestion = Object.assign({}, item);
    },

    closeQuestion() {
      this.$v.editedQuestion.$reset();
      this.dialog_questionnaire = false;
      this.clearQuestion();
      this.$nextTick(() => {
        this.editedQuestion = Object.assign({}, this.defaultQuestion);
        this.editedQuestionIndex = -1;
      });
    },

    clear() {
      this.$v.$reset();
      Object.assign(this.editedItem, this.defaultItem);
    },
    clearQuestion() {
      this.$v.editedQuestion.$reset();
      Object.assign(this.editedQuestion, this.defaultQuestion);
      this.editedQuestionIndex = -1;
    },
    addChoiceItems() {
      this.editedQuestion.choices.push('');
    },
    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    websocket() {
      // Socket.IO fetch data
      this.$options.sockets.sendData = (data) => {
        let action = data.action;

        if (
          action == "exam-create" ||
          action == "exam-edit" ||
          action == "exam-delete"
        ) {
          this.getExam();
        }
      };
    },
    intNumValFilter(evt) {
      evt = (evt) ? evt : window.event;
      let value = evt.target.value.toString() + evt.key.toString();

      if (!/^[-+]?[0-9]*?[0-9]*$/.test(value)) {
        evt.preventDefault();
      }
       else {

        return true;
      }
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "New Exam"
        : "Edit Exam";
    },
    titleErrors() {
      const errors = [];
      if (!this.$v.editedItem.title.$dirty) return errors;
      !this.$v.editedItem.title.required && errors.push("Title is required.");
      return errors;
    },
    descriptionErrors() {
      const errors = [];
      if (!this.$v.editedItem.description.$dirty) return errors;
      !this.$v.editedItem.description.required && errors.push("Description is required.");
      return errors;
    },
    passingScoreErrors() {
      const errors = [];
      if (!this.$v.editedItem.passing_score.$dirty) return errors;
      !this.$v.editedItem.passing_score.required && errors.push("Passing Score is required.");
      return errors;
    },
    examTypeErrors() {
      const errors = [];
      if (!this.$v.editedQuestion.type.$dirty) return errors;
      !this.$v.editedQuestion.type.required && errors.push("Exam Type is required.");
      return errors;
    },
    questionTextErrors() {
      const errors = [];
      if (!this.$v.editedQuestion.question_text.$dirty) return errors;
      !this.$v.editedQuestion.question_text.required && errors.push("Question Text is required.");
      return errors;
    },
    pointsErrors() {
      const errors = [];
      if (!this.$v.editedQuestion.points.$dirty) return errors;
      !this.$v.editedQuestion.points.required && errors.push("Points is required.");
      return errors;
    },
    activeStatus() {
      return this.editedItem.active ? 'Active' : 'Inactive';
    },
    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] = "Bearer " + localStorage.getItem("access_token");
    this.getExam();
  },
};
</script>