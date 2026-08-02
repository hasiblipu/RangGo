import { createContext, useContext, useState, useEffect } from 'react';
import { io } from 'socket.io-client';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [socket, setSocket] = useState(null);

  useEffect(() => {
    // Socket.io Connection Setup
    const newSocket = io('http://localhost:5000', {
      autoConnect: false,
    });
    setSocket(newSocket);

    // Auto Login check if token exists
    const token = localStorage.getItem('ranggo_token');
    if (token) {
      // আপনি চাইলে এখানে API কল করে ইউজার ডাটা চেক করতে পারেন
      setUser({ name: 'User' }); 
      newSocket.connect();
    }

    return () => newSocket.close();
  }, []);

  const login = (userData, token) => {
    localStorage.setItem('ranggo_token', token);
    setUser(userData);
    if (socket) socket.connect();
  };

  const logout = () => {
    localStorage.removeItem('ranggo_token');
    setUser(null);
    if (socket) socket.disconnect();
  };

  return (
    <AuthContext.Provider value={{ user, login, logout, socket }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);