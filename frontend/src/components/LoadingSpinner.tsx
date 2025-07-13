import type { CSSProperties } from 'react';

interface LoadingSpinnerProps {
  size?: 'small' | 'medium' | 'large';
  message?: string;
}

export default function LoadingSpinner({ size = 'medium', message = 'Loading...' }: LoadingSpinnerProps) {
  const spinnerSize = {
    small: '20px',
    medium: '40px',
    large: '60px'
  };

  const spinnerStyle: CSSProperties = {
    width: spinnerSize[size],
    height: spinnerSize[size],
    border: '3px solid #f3f3f3',
    borderTop: '3px solid #007bff',
    borderRadius: '50%',
    animation: 'spin 1s linear infinite',
    marginBottom: '1rem'
  };

  const containerStyle: CSSProperties = {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    minHeight: size === 'large' ? '100vh' : 'auto',
    color: '#666'
  };

  return (
    <div style={containerStyle}>
      <div style={spinnerStyle}></div>
      <div>{message}</div>
      <style>{`
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
}
