/**
 * Chat Client - Frontend JavaScript
 * Use this to communicate with the chat API endpoint
 */

class ChatClient {
    constructor(apiUrl = '/admin1/api/chat.php') {
        this.apiUrl = apiUrl;
        this.isProcessing = false;
    }

    /**
     * Send a chat message to the API
     * @param {string} message - The message to send
     * @returns {Promise<Object>} The API response
     */
    async sendMessage(message) {
        if (this.isProcessing) {
            throw new Error('Already processing a message');
        }

        if (!message || message.trim() === '') {
            throw new Error('Message cannot be empty');
        }

        this.isProcessing = true;

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message.trim() })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            return data;

        } catch (error) {
            console.error('Chat API Error:', error);
            throw error;
        } finally {
            this.isProcessing = false;
        }
    }

    /**
     * Check if a message is being processed
     * @returns {boolean}
     */
    isLoading() {
        return this.isProcessing;
    }
}

// Example usage:
/*
const chat = new ChatClient();

// Send a message
chat.sendMessage('Hello, I need help')
    .then(response => {
        console.log('Chat response:', response);
        // Display response.reply to user
    })
    .catch(error => {
        console.error('Failed to send message:', error);
        // Show error to user
    });
*/

// Make it globally available
window.ChatClient = ChatClient;
