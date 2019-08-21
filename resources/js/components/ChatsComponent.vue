<template>
  <div class="row">
    <div class="col-8">
      <div class="card card-default">
        <div class="card-header">Mensagens</div>
        <div class="card-body p-0">
          <ul class="list-unstyled" style="height:300px; overflow-y:scroll" v-chat-scroll>
            <li class="p-2" v-for="(message, index) in messages" :key="index">
              <strong>{{ message.user.name }}</strong>
              {{ message.message }}
            </li>
          </ul>
        </div>

        <input
          @keydown="sendTypingEvent"
          @keyup.enter="sendMessage"
          v-model="newMessage"
          type="text"
          name="message"
          placeholder="Enter your message..."
          class="form-control"
        />
      </div>
      <span class="text-muted" v-if="activeUser">{{ activeUser.name }} is typing...</span>
    </div>

    <div class="col-4">
      <div class="card card-default">
        <div class="card-header">Usuários Ativos</div>
        <div class="card-body">
          <ul>
            <li class="py-2" v-for="(user, index) in users" :key="index">{{ user.name }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"],

  data() {
    return {
      // inicialização das variáveis
      messages: [],
      newMessage: "",
      users: [],
      activeUser: false,
      typingTimer: false
    };
  },

  // executa ao carregar a página
  created() {
    this.fetchMessages();

    // ouve evento MessageSent
    Echo.join("chat")
      // informações dos usuários que estão conectados na página chats
      .here(user => {
        this.users = user;
      })
      // verifica quando o usuário entrou na página
      .joining(user => {
        this.users.push(user);
      })
      // verifica quando o usuário saiu da página
      .leaving(user => {
        this.users = this.users.filter(u => u.id != user.id);
      })
      .listen("MessageSent", event => {
        this.messages.push(event.message);
      })
      // mostra o nome do usuário que está digitando
      .listenForWhisper("typing", user => {
        this.activeUser = user;

        // quando receber outro evento de digitação reinicia o temporizador
        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }

        // após 3 segundos setta para falso mensagem de digitação do usuário
        this.typingTimer = setTimeout(() => {
          this.activeUser = false;
        }, 3000);
      });
  },

  methods: {
    // buscar mensagens
    fetchMessages() {
      axios.get("messages").then(response => {
        this.messages = response.data;
      });
    },

    // enviar mensagem
    sendMessage() {
      this.messages.push({
        user: this.user,
        message: this.newMessage
      });

      // envia request para o controller
      axios.post("messages", { message: this.newMessage });

      this.newMessage = "";
    },

    // ouve a digitação do usuário que está logado
    sendTypingEvent() {
      Echo.join("chat").whisper("typing", this.user);
    }
  }
};
</script>
