import React, { useState, useEffect, useRef } from 'react';
import './ChatDialog.css';

function ChatDialog({ onCloseClick, isDialogOpen }) {
  const [messages, setMessages] = useState([]);
  const [socket, setSocket] = useState(null);
  const [messageInput, setMessageInput] = useState('');
  const [isWebSocketOpen, setIsWebSocketOpen] = useState(false);
  const inputRef = useRef(null);
  const messageContainerRef = useRef(null);

  const startWebSocketServer = () => {
    fetch('http://shop.loc/wp-json/chip/v1/server', {
      method: 'POST',
      headers: {
        'X-WP-Nonce': ChipData.nonce
      }
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to start WebSocket server: ' + response.statusText);
        }

        openWebSocket();
      })
      .catch(error => {
        console.error('Error:', error);
      });
  };

  const openWebSocket = () => {
    const newSocket = new WebSocket('ws://shop.loc:8082');

    newSocket.addEventListener('open', (event) => {
      setIsWebSocketOpen(true);
      setMessages((messages) => [...messages, 'Connected to chat']);
    });

    newSocket.addEventListener('close', (event) => {
      setIsWebSocketOpen(false);
      setMessages((messages) => [...messages, 'Chat closed']);
    });

    newSocket.addEventListener('error', (event) => {
      setMessages((messages) => [...messages, event.data]);
    });

    newSocket.addEventListener('message', (event) => {
      setMessages((messages) => [...messages, event.data]);
    });

    setSocket(newSocket);
  };

  useEffect(() => {
    if (isDialogOpen) {
      startWebSocketServer();
    }
  }, [isDialogOpen]);

  useEffect(() => {
    if (messageContainerRef.current) {
      messageContainerRef.current.scrollTop = messageContainerRef.current.scrollHeight;
    }
  }, [messages]);

  const handleSendMessage = () => {
    if (messageInput.trim() !== '') {
      if (!isWebSocketOpen) {
        startWebSocketServer();
      }

      const messageWithUsername = `${ChipData.user}: ${messageInput}`;
      socket.send(messageWithUsername);
      setMessages((messages) => [...messages, messageWithUsername]);
      setMessageInput('');

      if (messageContainerRef.current) {
        messageContainerRef.current.scrollTop = messageContainerRef.current.scrollHeight;
      }

      inputRef.current.focus();
    }
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
      handleSendMessage();
    }
  };

  return (
    <div className={`chat-dialog ${isDialogOpen ? 'open' : 'closed'}`}>
      {isDialogOpen && (
        <>
          <button className="close-button" onClick={onCloseClick}> X </button>
          <div className="chat-messages" ref={messageContainerRef}>
            {messages.map((message, index) => (
              <div className="message" key={index}>{message}</div>
            ))}
          </div>
          <div className="chat-input">
            <input
              type="text"
              placeholder="Type your message ..."
              value={messageInput}
              onChange={(e) => setMessageInput(e.target.value)}
              onKeyPress={handleKeyPress}
              autoFocus
              ref={inputRef}
            />
            <button onClick={handleSendMessage}>Send</button>
          </div>
        </>
      )}
    </div>
  );
}

export default ChatDialog;
