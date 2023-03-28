import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { forkJoin, Observable, throwError } from 'rxjs';
import { User } from '../interface/userData';
import { map } from 'rxjs/operators';
import { retry, catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class UserService {
  //API Urls
  // private apiUrl = 'http://localhost/test-hcl-php/api';
  private apiUrl = 'http://localhost/backend-project/';

  constructor(private http: HttpClient) {}
  
  /**
   * Gets csv data - To get all existing data from CSV
   * @returns csv data 
   */
  getUsers(): Observable<User[]> {
    return this.http
      .get(this.apiUrl)
      .pipe<User[]>(map((data: any) => data.data))
      .pipe(retry(1), catchError(this.handleError));
  }

  //To Update single data row
  updateUser(user: User): Observable<User> {
    return this.http.patch<User>(`${this.apiUrl}`, user);
  }

  //Adds new entry in CSV
  addUser(user: User): Observable<User> {
    return this.http.post<User>(`${this.apiUrl}`, user);
  }

  //To delete single entry from CSV
  deleteUser(id: number): Observable<User> {
    return this.http.delete<User>(`${this.apiUrl}${id}`);
  }

  //To delete multiple selected data from CSV
  deleteUsers(users: User[]): Observable<User[]> {
    return forkJoin(
      users.map((user) =>
        this.http.delete<User>(`${this.apiUrl}/${user.id}`)
      )
    );
  }

  //Exception handler
  handleError(error:any) {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      // client-side error
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // server-side error
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(() => {
        return errorMessage;
    });
  }
}
