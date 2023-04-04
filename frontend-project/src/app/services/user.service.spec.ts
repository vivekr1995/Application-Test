import { UserService } from './user.service';
import { HttpClient } from '@angular/common/http';
import { MatSnackBar } from '@angular/material/snack-bar';

/**
 * Service Test
 */
describe('UserService', () => {
  let service: UserService;
  let fakeHttpClient: jasmine.SpyObj<HttpClient>;
  let fakeMatSnackBar: jasmine.SpyObj<MatSnackBar>;

  function createService() {
    service = new UserService(
      fakeHttpClient,
      fakeMatSnackBar
    );
  }

  beforeEach(() => {
    fakeHttpClient = jasmine.createSpyObj<HttpClient>('HttpClient', ['post', 'get']);

    createService();
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});