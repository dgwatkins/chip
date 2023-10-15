import React, { useState } from 'react';
import FloatingChatIcon from './FloatingChatIcon';
import ChatDialog from './ChatDialog';

function App() {
  const [isDialogOpen, setDialogOpen] = useState(false);

  const handleDialogToggle = () => {
    setDialogOpen(!isDialogOpen);
  };

  return (
    <div>
      <FloatingChatIcon onIconClick={handleDialogToggle} />
      {isDialogOpen && <ChatDialog onCloseClick={handleDialogToggle} isDialogOpen={isDialogOpen} />}
    </div>
  );
}

export default App;
