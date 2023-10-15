import React from 'react';
import './FloatingChatIcon.css';

function FloatingChatIcon({ onIconClick }) {
  return (
    <div id="chat-icon-container" onClick={onIconClick}>
      Click to Chat
    </div>
  );
};

export default FloatingChatIcon;
