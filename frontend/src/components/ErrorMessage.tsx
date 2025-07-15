interface ErrorMessageProps {
  message: string;
  onRetry?: () => void;
}

export default function ErrorMessage({ message, onRetry }: ErrorMessageProps) {
  return (
    <div style={{
      background: '#f8d7da',
      color: '#721c24',
      padding: '1rem',
      borderRadius: '8px',
      border: '1px solid #f5c6cb',
      margin: '1rem 0',
      textAlign: 'center'
    }}>
      <p style={{ margin: '0 0 1rem 0' }}>⚠️ {message}</p>
      {onRetry && (
        <button 
          onClick={onRetry}
          style={{
            background: '#dc3545',
            color: 'white',
            border: 'none',
            padding: '0.5rem 1rem',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Try Again
        </button>
      )}
    </div>
  );
}
