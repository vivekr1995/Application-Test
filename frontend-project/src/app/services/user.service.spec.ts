import { TestBed } from '@angular/core/testing';
import { UserService } from './user.service';
import { HttpClient } from '@angular/common/http';

/**
 * Service Test
 */
describe('UserService', () => {
  let service: UserService;
  let fakeHttpClient: jasmine.SpyObj<HttpClient>;

  function createService() {
    service = new UserService(
      fakeHttpClient,
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