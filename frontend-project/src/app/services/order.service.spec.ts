import { OrderService } from './order.service';
import { HttpClient } from '@angular/common/http';
import { MatSnackBar } from '@angular/material/snack-bar';

/**
 * Service Test
 */
describe('OrderService', () => {
  let service: OrderService;
  let fakeHttpClient: jasmine.SpyObj<HttpClient>;
  let fakeMatSnackBar: jasmine.SpyObj<MatSnackBar>;

  function createService() {
    service = new OrderService(
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