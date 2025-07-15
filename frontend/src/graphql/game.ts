import { gql } from '@apollo/client';

export const CURRENT_GAME = gql`
  query CurrentGame {
    currentGame {
      id
      userId
      timelineAccuracy
      isCompleted
      createdAt
      completedAt
      people {
        id
        gameStateId
        name
        deathDate
      }
    }
  }
`;

export const START_NEW_GAME = gql`
  mutation StartNewGame($userId: Int!) {
    startNewGame(userId: $userId) {
      id
      userId
      timelineAccuracy
      isCompleted
      createdAt
      completedAt
      people {
        id
        gameStateId
        name
        deathDate
      }
    }
  }
`;

export const SAVE_PERSON = gql`
  mutation SavePerson($gameStateId: Int!, $personId: Int!) {
    savePerson(gameStateId: $gameStateId, personId: $personId)
  }
`;

export const END_GAME = gql`
  mutation EndGame($gameStateId: Int!) {
    endGame(gameStateId: $gameStateId) {
      gameStateId
      timelineAccuracy
      eventResults
      peopleSaved
      totalPeople
      message
    }
  }
`;
