<script>
    // Configuración del chatbot de Udify (Dify)
    window.difyChatbotConfig = {
        token: 'B4keNRHr22WXJT38',
        systemVariables: {
            // user_id: 'YOU CAN DEFINE USER ID HERE',
            // conversation_id: 'YOU CAN DEFINE CONVERSATION ID HERE, IT MUST BE A VALID UUID',
        },
    }
</script>
<!-- Script de integración del chatbot Udify -->
<script src="https://udify.app/embed.min.js" id="B4keNRHr22WXJT38" defer>
</script>
<style>
    /* Estilos para el botón flotante del chatbot */
    #dify-chatbot-bubble-button {
        background-color: #1C64F2 !important;
        position: fixed !important;
        z-index: 10000 !important;
    }

    /* Estilos para la ventana flotante del chatbot */
    #dify-chatbot-bubble-window {
        width: 24rem !important;
        height: 40rem !important;
        position: fixed !important;
        z-index: 9999 !important;
    }
</style>