import { createContext, useContext, useEffect, useState } from 'react';
import type { ReactNode } from 'react';
import { useQuery } from '@apollo/client';
import { ME_QUERY } from '../graphql/me';

interface User {
  id: string;
  email: string;
  username: string;
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  refetch: () => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider = ({ children }: AuthProviderProps) => {
  const { data, loading, refetch, error } = useQuery(ME_QUERY, {
    errorPolicy: 'ignore', // Don't throw errors for unauthenticated users
    notifyOnNetworkStatusChange: true,
  });
  
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    if (data?.me) {
      setUser(data.me);
    } else {
      setUser(null);
    }
  }, [data]);

  // Log network errors for debugging
  useEffect(() => {
    if (error && error.networkError) {
      console.error('Auth network error:', error.networkError);
    }
  }, [error]);

  const value = {
    user,
    loading,
    refetch,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};
