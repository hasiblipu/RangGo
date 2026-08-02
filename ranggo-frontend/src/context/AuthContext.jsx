import { createContext, useContext, useState, useEffect } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);

  useEffect(() => {
    // Auto Login check if token exists
    const token = localStorage.getItem('ranggo_token');

    if (token) {
      // চাইলে এখানে API কল করে ইউজারের তথ্য যাচাই করতে পারেন
      setUser({ name: 'User' });
    }
  }, []);

  const login = (userData, token) => {
    localStorage.setItem('ranggo_token', token);
    setUser(userData);
  };

  const logout = () => {
    localStorage.removeItem('ranggo_token');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
