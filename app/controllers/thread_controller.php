<?php
class ThreadController extends AppController
{
    const THREADS_PER_PAGE = 5;
    const USERS_PER_PAGE = 5;
    const SEARCH_BY_THREAD = 'Thread';
    const SEARCH_BY_USER = 'User';

    /**
     *VIEW ALL THREADS OR USERS
     */        
    public function index()
    {
        check_user_logged_out();
        $user = User::get($_SESSION['user_id']);
        $cur_page = Param::get('pn');
        $search_item = Param::get('search_item', null);
        $placeholder = 'Enter a thread title here';
        // FOR FILTERING RESULTS OF THREADS
        $filter_by = Param::get('filter_by', Thread::ALL_THREADS);
        $filter_options = array(
            Thread::ALL_THREADS, 
            Thread::MY_THREADS, 
            Thread::COMMENTED_THREADS, 
            Thread::THEIR_THREADS
        );
        // FOR SEARCHING THREADS OR USERS
        $search_by = Param::get('search_by', Thread::SEARCH_BY_THREAD);
        $search_options = array(
            Thread::SEARCH_BY_THREAD, 
            Thread::SEARCH_BY_USER
        );
        // FOR SORTING OF THREADS
        $order_by = Param::get('order_by', Thread::LATEST_FIRST);
        $order_options = array(
            Thread::LATEST_FIRST,
            Thread::OLDEST_FIRST,
            Thread::MOST_COMMENTS,
            Thread::LEAST_COMMENTS,
            Thread::MOST_LIKES,
            Thread::LEAST_LIKES
        );
        if (isset($search_item) && !$search_item && $filter_by == 'All Threads' && $search_by == 'Thread' 
            && $order_by == 'Latest First') {
            redirect('thread', 'index');
        }
        $num_rows = Thread::count($search_item, $filter_by, $user->user_id);
        $pagination = pagination($num_rows, $cur_page, self::THREADS_PER_PAGE);
        // TODO: Get all threads
        $threads = Thread::getAll($pagination['limit'], $search_item, $filter_by, $order_by, $user->user_id);
        // IF SEARCH OPTION IS USER, ONLY USER INFORMATION IS SEEN
        if ($search_by == 'User') {
            $num_rows = User::countFound($search_item);
            $pagination = pagination($num_rows, $cur_page, self::USERS_PER_PAGE);
            $users_found = User::search($search_item, $pagination['limit']);
            $placeholder = 'Enter a user\'s username here';
        }
        $this->set(get_defined_vars());
    }
    
    /**
     *LIKE OR DISLIKE A THREAD
     */
    public function addLikeDislike()
    {
        check_user_logged_out();
        $like_status = Param::get('like_status');
        $home_page = Param::get('home_page');
        $thread = Thread::get(Param::get('thread_id'));
        $thread->user_id = $_SESSION['user_id'];
        $thread->addLikeDislike($like_status); 
        redirect($home_page, null);
        $this->set(get_defined_vars());
    }

    /**
     *CREATE A NEW THREAD
     */
    public function create()
    {
        check_user_logged_out();
        $thread = new Thread();
        $thread_title = Param::get('thread_title');
        $description = Param::get('description');
        if (isset($thread_title) && isset($description)) {
            try {
                $thread->title = $thread_title;
                $thread->user_id = $_SESSION['user_id'];
                $thread->description = $description;
                $new_thread_id = $thread->create();
                redirect('comment', 'view', array('thread_id' => $new_thread_id));
            } catch (ValidationException $e) {
                
            }                
        }
        $this->set(get_defined_vars());
    }

    /**
     *EDIT A THREAD
     */
    public function edit()
    {            
        check_user_logged_out();
        $thread = Thread::get(Param::get('thread_id'));
        $old_title = $thread->title;
        $new_title = Param::get('title');
        $old_description = $thread->description;
        $new_description = Param::get('description');
        if (isset($new_title) || isset($new_description)) {
            try {
                $thread->title = $new_title;
                $thread->description = $new_description;
                $thread->update();
                redirect('comment', 'view', array('thread_id' => $thread->thread_id));
            } catch (ValidationException $e) {
                
            }
        }
        $this->set(get_defined_vars());
    }
    
    /**
     *DELETE A THREAD
     */
    public function delete()
    {
        check_user_logged_out();
        $thread = Thread::get(Param::get('thread_id'));
        $thread->delete(); 
        redirect('thread', 'index');
        $this->set(get_defined_vars());
    }
}
