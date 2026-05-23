import { TestBed, fakeAsync, tick, flush } from '@angular/core/testing';
import { AutoLogoutService } from './auto-logout';
import { AuthService } from './auth.service';
import { Router } from '@angular/router';
import { ToastService } from './toast.service';
import { of, throwError } from 'rxjs';
import { NgZone } from '@angular/core';

describe('AutoLogoutService', () => {
  let service: AutoLogoutService;
  let authServiceSpy: jasmine.SpyObj<AuthService>;
  let routerSpy: jasmine.SpyObj<Router>;
  let toastServiceSpy: jasmine.SpyObj<ToastService>;
  let ngZone: NgZone;

  beforeEach(() => {
    const authSpy = jasmine.createSpyObj('AuthService', ['isLoggedIn', 'logout']);
    const routerMock = jasmine.createSpyObj('Router', ['navigate']);
    const toastSpy = jasmine.createSpyObj('ToastService', ['info']);

    TestBed.configureTestingModule({
      providers: [
        AutoLogoutService,
        { provide: AuthService, useValue: authSpy },
        { provide: Router, useValue: routerMock },
        { provide: ToastService, useValue: toastSpy }
      ]
    });

    authServiceSpy = TestBed.inject(AuthService) as jasmine.SpyObj<AuthService>;
    routerSpy = TestBed.inject(Router) as jasmine.SpyObj<Router>;
    toastServiceSpy = TestBed.inject(ToastService) as jasmine.SpyObj<ToastService>;
    ngZone = TestBed.inject(NgZone);
  });

  it('should be created', () => {
    service = TestBed.inject(AutoLogoutService);
    expect(service).toBeTruthy();
  });

  it('should logout user after inactivity', fakeAsync(() => {
    authServiceSpy.isLoggedIn.and.returnValue(true);
    authServiceSpy.logout.and.returnValue(of({ message: 'Logged out' }));
    
    // Instantiate service to start listener
    service = TestBed.inject(AutoLogoutService);

    // Simulate activity
    window.dispatchEvent(new Event('mousemove'));
    
    // Wait for 5 minutes
    tick(5 * 60 * 1000);

    expect(authServiceSpy.logout).toHaveBeenCalled();
    expect(toastServiceSpy.info).toHaveBeenCalledWith('Session expired due to inactivity.');
    expect(routerSpy.navigate).toHaveBeenCalledWith(['/']);
    
    service.stopMonitoring();
    flush();
  }));

  it('should reset timer on activity', fakeAsync(() => {
    authServiceSpy.isLoggedIn.and.returnValue(true);
    authServiceSpy.logout.and.returnValue(of({ message: 'Logged out' }));
    
    service = TestBed.inject(AutoLogoutService);

    // First activity
    window.dispatchEvent(new Event('mousemove'));
    tick(3 * 60 * 1000); // 3 mins passed

    // Second activity (resets timer)
    window.dispatchEvent(new Event('keydown'));
    tick(3 * 60 * 1000); // Another 3 mins passed (total 6, but only 3 since last activity)

    expect(authServiceSpy.logout).not.toHaveBeenCalled();

    tick(2 * 60 * 1000); // Final 2 mins (total 5 since last activity)
    expect(authServiceSpy.logout).toHaveBeenCalled();

    service.stopMonitoring();
    flush();
  }));

  it('should handle logout error gracefully', fakeAsync(() => {
    authServiceSpy.isLoggedIn.and.returnValue(true);
    authServiceSpy.logout.and.returnValue(throwError(() => new Error('API Error')));
    
    // Mock signal behavior if needed, but here we just check navigation
    authServiceSpy.currentUser = { set: jasmine.createSpy('set') } as any;

    service = TestBed.inject(AutoLogoutService);
    window.dispatchEvent(new Event('click'));
    
    tick(5 * 60 * 1000);

    expect(routerSpy.navigate).toHaveBeenCalledWith(['/']);
    
    service.stopMonitoring();
    flush();
  }));
});
