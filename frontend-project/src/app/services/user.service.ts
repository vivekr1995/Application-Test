import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { forkJoin, Observable, throwError } from 'rxjs';
import { User } from '../interface/userData';
import { map } from 'rxjs/operators';
import { retry, catchError } from 'rxjs/operators';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class UserService {
  
  /**
   * Getting data from environment
   */
  apiUrl = environment.apiUrl;

  /**
   * Creates instance
   * @param http 
   */
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

  /**
   * Adds new entry in CSV
   * @param user 
   * @returns User 
   */
  addUser(user: User): Observable<User> {
    return this.http.post<User>(`${this.apiUrl}`, user);
  }

  /**
   * To Update single data row
   * @param user 
   * @returns User 
   */
  updateUser(user: User): Observable<User> {
    return this.http.patch<User>(`${this.apiUrl}`, user);
  }

  /**
   * To delete single entry from CSV
   * @param id 
   * @returns User 
   */
  deleteUser(id: number): Observable<User> {
    return this.http.delete<User>(`${this.apiUrl}${id}`);
  }

  /**
   * To delete multiple selected data from CSV
   * @param id 
   * @returns User 
   */
  deleteUsers(users: User[]): Observable<User[]> {
    return forkJoin(
      users.map((user) =>
        this.http.delete<User>(`${this.apiUrl}/${user.id}`)
      )
    );
  }

  /**
   * Exception handler
   * @param error
   * @returns Error message 
   */
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
